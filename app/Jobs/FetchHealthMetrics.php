<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notifiable;
use Webklex\IMAP\Facades\Client;
use App\Notifications\HealthMetricsUpdate;
use App\Notifications\HealthMetricsHRUpdate;
use App\User;
use App\ApprovalAuthority;
use App\HealthMetricsMc;
use App\HealthMetricsCheckin;
use App\Holiday;
use App\LeaveApplication;
use App\LeaveBalance;
use App\TakenLeave;
use Notification;
use DateTime;

class FetchHealthMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = Client::account('gmail'); // Setup imap.php
        $client->connect();

        $inbox = $client->getFolder('INBOX');

        $dateToday = date('d.m.Y');

        // $mails = $inbox->messages()->unanswered()->since('02.03.2020')->subject('HMS medical certificate issued')->get();
        $mails = $inbox->messages()->unanswered()->since($dateToday)->subject('HMS Medical Certificate issued')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('HMS medical certificate issued')->get();

        foreach($mails as $mail){
            $body = $mail->getHTMLBody();

            (date('j') < 10) ? $countDate = 1 : $countDate = 2;
            (date('n') < 10) ? $countMonth = 1 : $countMonth = 2;

            // Find employee ID.
            $staffIDPos = strpos($body, ': IF');
            $staffID = substr($body, $staffIDPos +2, 6);
            // dd($staffID);

            // Find clinic name.
            $clinicNameStartPos = strpos($body, '<strong>') + 8;
            $clinicNameEndPos = strpos($body, '</strong> has just issued');
            $stringLength = $clinicNameEndPos - $clinicNameStartPos;
            $clinicNameX = substr($body, $clinicNameStartPos, $stringLength);
            $clinicName = str_replace('&amp;', '&', $clinicNameX);
            // dd($clinicName);

            // Find total days.
            $totalDaysPos = strpos($body, '-day');
            $totalDaysStr = substr($body, $totalDaysPos -2, 2);
            $totalDays = trim(preg_replace('/\D/', '', $totalDaysStr));

            // Find leave date from.
            $leaveFromPos = strpos($body, '(MC) from');
            $leaveFromStr = substr($body, $leaveFromPos + 10, $countDate + $countMonth + 6); // Receiving format Y/d/m.
            $dFrom = explode('/',$leaveFromStr);
            $leaveFrom = date('Y-m-d', strtotime($dFrom[1].'/'.$dFrom[0].'/'.$dFrom[2])); // Convert to Y-m-d.
            $validLF = DateTime::createFromFormat('Y-m-d', $leaveFrom);
            $isLFValid = ($validLF && $validLF->format('Y-m-d') === $leaveFrom); // Check is a date.

            // Find leave date to.
            $leaveToPos = strpos($body, '/'.date('Y').' to <strong>');
            $leaveToStr = substr($body, $leaveToPos + 17 , $countDate + $countMonth + 6); // Receiving format Y/d/m.
            $dTo = explode('/',$leaveToStr);
            $leaveTo = date('Y-m-d', strtotime($dTo[1].'/'.$dTo[0].'/'.$dTo[2])); // // Convert to Y-m-d.
            $validLT = DateTime::createFromFormat('Y-m-d', $leaveTo);
            $isLTValid = ($validLT && $validLT->format('Y-m-d') === $leaveTo); // Check is a date.

            // Find MC link.
            $htmlLink = $mail->getHTMLBody();
            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $htmlLink, $result);
            if (!empty($result)) {
                $link = (!empty($result['href'][1])) ? $link = $result['href'][1] : $link = ''; // Got 2 links, the second one is the printable link.
            }

            // Get user using employee ID from mail.
            $emp = User::where('staff_id', trim(preg_replace('/[\(\)]/', '', $staffID)))->first();

            $plusDay = 0;
            $error = 0;
            $leaveBal = ($emp != null) ? $emp->leave_balances[2]->no_of_days : 0; // Sick Leave balance.

            if (($emp != null) && ($isLFValid == true) && ($isLTValid == true) && (is_numeric($totalDays) == true)) {
                if ($leaveBal >= $totalDays) {

                    // To get resume date.
                    do {
                        $plusDay += 1;
                        $nextWorkDay = date('Y-m-d', strtotime($leaveTo.' +'.$plusDay.' Weekday'));
                        $publicHolidays = Holiday::where('date_from', $nextWorkDay)->orWhere('date_to', $nextWorkDay)->get();
                    } while ($publicHolidays->isEmpty() == false);

                    $leaveApp = new LeaveApplication;
                    $leaveApp->user_id = $emp->id;
                    $leaveApp->leave_type_id = 3; // Sick Leave
                    $leaveApp->status = 'APPROVED'; // Default Approved
                    $leaveApp->approver_id_1 = $emp->approval_authority->authority_1_id;
                    $leaveApp->approver_id_2 = $emp->approval_authority->authority_2_id;
                    $leaveApp->approver_id_3 = $emp->approval_authority->authority_3_id;
                    $leaveApp->date_from = $leaveFrom;
                    $leaveApp->date_to = $leaveTo;
                    $leaveApp->date_resume = $nextWorkDay;
                    $leaveApp->total_days = $totalDays;

                    if (intval($totalDays) < 1) {
                        $leaveApp->apply_for = 'half-day-am';
                    } else {
                        $leaveApp->apply_for = 'full-day';
                    }

                    $leaveApp->reason = 'Health Metrics Auto Apply';
                    $leaveApp->relief_personnel_id = null;
                    $leaveApp->emergency_contact_name = $emp->emergency_contact_name;
                    $leaveApp->emergency_contact_no = $emp->emergency_contact_no;
                    $leaveApp->save();


                    $leaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 3)->first();
                    $leaveBal->no_of_days = $leaveBal->no_of_days - intval($totalDays);
                    $leaveBal->update();

                    //SUBSTRACT HOSPITALIZATION BALANCE. ADDED BY FAIZUL
                    //DISABLE AS OF 1 JAN 2023
                    // $hospLeaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 4)->first();
                    // $hospLeaveBal->no_of_days = $hospLeaveBal->no_of_days - intval($totalDays);
                    // $hospLeaveBal->update();

                    $takenLeave = TakenLeave::where('user_id', $emp->id)->where('leave_type_id', 3)->first();
                    $takenLeave->no_of_days = $takenLeave->no_of_days + intval($totalDays);
                    $takenLeave->update();

                    $last_checkin = HealthMetricsCheckin::orderBy('id', 'DESC')->where('user_id', $emp->id)->where('clinic_name', $clinicName)->first();
                    
                    $hm = new HealthMetricsMc;
                    $hm->user_id = $emp->id;
                    $hm->application_id = $leaveApp->id;
                    $hm->checkin_id = ($last_checkin != null) ? $last_checkin->id : null;
                    $hm->clinic_name = $clinicName;
                    $hm->leave_from = $leaveFrom;
                    $hm->leave_to = $leaveTo;
                    $hm->total_days = $totalDays;
                    $hm->status = 'Auto Applied';
                    $hm->link = $link;
                    $hm->save();
                } else {
                    $error += 1;
                }
            } else {
                $error += 1;
            }

            // Get admin users to notify the affected employees.
            $admins = User::where('user_type', 'Admin')->get();
            
            if ($error == 0) {
                $healthUpdate = [
                    'name' => $emp->name,
                    'date_from' => $leaveFrom,
                    'date_to' => $leaveTo,
                    'total_days' => $totalDays,
                ];
                
                $emp->notify(new HealthMetricsUpdate($healthUpdate)); 

                $authorities = ApprovalAuthority::where('user_id', $emp->id)->first();
                $supervisor = $authorities->authority_one;

                $healthUpdateSupervisor = [
                    'hr_name' => $supervisor->name,
                    'name' => $emp->name,
                    'date_from' => $leaveFrom,
                    'date_to' => $leaveTo,
                    'total_days' => $totalDays,
                    'status' => 'success',
                ];

                $supervisor->notify(new HealthMetricsHRUpdate($healthUpdateSupervisor));

                foreach($admins as $admin) {
                    $healthUpdateHR = [
                        'hr_name' => $admin->name,
                        'name' => $emp->name,
                        'date_from' => $leaveFrom,
                        'date_to' => $leaveTo,
                        'total_days' => $totalDays,
                        'status' => 'success',
                    ];

                    $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
                }
            } else {
                foreach($admins as $admin) {
                    $healthUpdateHR = [
                        'hr_name' => $admin->name,
                        'mail' => $body,
                        'status' => 'fail',
                    ];

                    $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
                }
            }

            $mail->setFlag('answered');

        }
    }
}
