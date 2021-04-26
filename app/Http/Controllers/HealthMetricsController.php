<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Webklex\IMAP\Facades\Client;
use App\Notifications\HealthMetricsUpdate;
use App\Notifications\HealthMetricsHRUpdate;
use App\User;
use App\HealthMetricsMc;
use App\HealthMetricsCheckin;
use App\Holiday;
use App\LeaveApplication;
use App\LeaveBalance;
use App\TakenLeave;
use App\History;
use Notification;
use DateTime;

class HealthMetricsController extends Controller
{
    public function index() {
        $checkins = HealthMetricsCheckin::orderBy('id', 'DESC')->paginate(15);

        return view('admin.healthmetrics')->with(compact('checkins'));
    }

    public function search(Request $request) {
        $name = $request->get('name');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        $query = HealthMetricsCheckin::join('users', 'health_metrics_checkins.user_id', '=', 'users.id');
        
        
        if ($name != null) {
            $query->where('users.name', $name);
        }
        
        if ($date_from != null && $date_to != null) {
            $query->join('health_metrics_mcs', 'health_metrics_checkins.id', '=', 'health_metrics_mcs.checkin_id')
                  ->whereBetween('health_metrics_mcs.leave_from', [$date_from, $date_to])
                  ->orWhereBetween('health_metrics_mcs.leave_to', [$date_from, $date_to]);
        }
         
        $checkins = $query->select('health_metrics_checkins.*')->paginate(15);
        // dd($checkins);

        return view('admin.healthmetrics')->with(compact('checkins'));
    }

    public function fetch() {
        $client = Client::account('gmail'); // Setup imap.php
        $client->connect();
        
        $inbox = $client->getFolder('INBOX');
        // dd($client);
    
        $dateToday = date('d.m.Y');

        // Get admin users to notify the affected employees.
        $admins = User::where('user_type', 'Admin')->get();
        
        // $mails = $inbox->messages()->unanswered()->since('02.03.2020')->subject('HMS medical certificate issued')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('HMS medical certificate issued')->get();
        $mails = $inbox->messages()->since('22.04.2021')->subject('HMS medical certificate issued')->get();
        // dd(count($mails));
        
        foreach($mails as $mail){
            // $mail->setFlag('answered');
            $body = $mail->getHTMLBody();
            // dd($mail);
            // dd($body);
            
            (date('j') < 10) ? $countDate = 1 : $countDate = 2; 
            (date('n') < 10) ? $countMonth = 1 : $countMonth = 2;
            
            // Find employee ID.
            $staffIDPos = strpos($body, ': IF');
            $staffID = substr($body, $staffIDPos +2, 6);

            // Find clinic name.
            $clinicNameStartPos = strpos($body, '<strong>') + 8;
            $clinicNameEndPos = strpos($body, '</strong> has just issued');
            $stringLength = $clinicNameEndPos - $clinicNameStartPos;
            $clinicNameX = substr($body, $clinicNameStartPos, $stringLength);
            $clinicName = str_replace('&amp;', '&', $clinicNameX);
            // dd($clinicName);
            // todo add clinic name to hm mcs
            
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
                // echo $emp->name , $emp->leave_balances[2]->no_of_days;
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

            // if ($error == 0) {
            //     $healthUpdate = [
            //         'name' => $emp->name,
            //         'date_from' => $leaveFrom, 
            //         'date_to' => $leaveTo, 
            //         'total_days' => $totalDays, 
            //     ];
                
                // $emp->notify(new HealthMetricsUpdate($healthUpdate));

            //     foreach($admins as $admin) {
            //         $healthUpdateHR = [
            //             'hr_name' => $admin->name,
            //             'name' => $emp->name,
            //             'date_from' => $leaveFrom, 
            //             'date_to' => $leaveTo, 
            //             'total_days' => $totalDays, 
            //             'status' => 'success',
            //         ];
                    
                    // $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
            //     }
            // } else {
            //     foreach($admins as $admin) {
            //         $healthUpdateHR = [
            //             'hr_name' => $admin->name,
            //             'mail' => $body,
            //             'status' => 'fail',
            //         ];

                    // $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
            //     }
            // }
            
            // $mail->setFlag('answered');

        }
        
        return response()->json(['success'=>'Mail fetched successfully!']);
    }

    public function revert(Request $request){
        $application_id = $request->get('application_id');

        $la = LeaveApplication::where('id', $application_id)->first();
        $la->status = 'CANCELLED';
        $la->remarker_id = auth()->user()->id;

        $hm = HealthMetricsMc::where('application_id', $application_id)->first();
        $hm->status = 'Reverted';
        
        $leaveBal = LeaveBalance::where('user_id', $la->user_id)->where('leave_type_id', 3)->first();
        $leaveBal->no_of_days = $leaveBal->no_of_days + $la->total_days;
        
        $takenLeave = TakenLeave::where('user_id', $la->user_id)->where('leave_type_id', 3)->first();
        $takenLeave->no_of_days = $takenLeave->no_of_days - $la->total_days;

        $la->update();
        $hm->update();
        $leaveBal->update();
        $takenLeave->update();

        $hist = new History;
        $hist->leave_application_id = $application_id;
        $hist->user_id = auth()->user()->id;
        $hist->action = 'Cancelled';
        $hist->remarks = 'Health Metrics Auto Apply';
        $hist->save();

        return response()->json(['application_id' => $application_id]);
    }

    public function fetch_checkins() {
        $client = Client::account('gmail'); // Setup imap.php
        $client->connect();
        
        $inbox = $client->getFolder('INBOX');
    
        $dateToday = date('d.m.Y');

        // Get admin users to notify the affected employees.
        $admins = User::where('user_type', 'Admin')->get();
        
        $mails = $inbox->messages()->unanswered()->since('22.04.2021')->subject('Employee check-in with HMS')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('HMS medical certificate issued')->get();
        // $mails = $inbox->messages()->unanswered()->since($dateToday)->subject('Employee check-in with HMS')->get();

        foreach($mails as $mail){
            $body = $mail->getHTMLBody();
            // dd($body);

            (date('j') < 10) ? $countDate = 1 : $countDate = 2; 
            (date('n') < 10) ? $countMonth = 1 : $countMonth = 2;
            
            // Find employee ID.
            $staffIDPos = strpos($body, ': IF');
            $staffID = substr($body, $staffIDPos +2, 6);
            // dd($staffID);
            
            // Find clinic name.
            $clinicNameStartPos = strpos($body, 'checked-in to <strong>') + 22;
            $clinicNameEndPos = strpos($body, '</strong>.');
            $stringLength = $clinicNameEndPos - $clinicNameStartPos;
            $clinicNameX = substr($body, $clinicNameStartPos, $stringLength);
            $clinicName = str_replace('&amp;', '&', $clinicNameX);
            // dd($clinicName);
            
            // Get check in date.
            $checkInPos = strpos($body, 'CHECK-IN TIME</span></span><br>') + 138;
            $checkInDateStr = substr($body, $checkInPos, $countDate + $countMonth + 6); // Receiving format Y/d/m.
            $dCheckIn = explode('/',$checkInDateStr);
            $checkInDate = date('Y-m-d', strtotime($dCheckIn[1].'/'.$dCheckIn[0].'/'.$dCheckIn[2])); // Convert to Y-m-d.
            $validCD = DateTime::createFromFormat('Y-m-d', $checkInDate);
            $isCDValid = ($validCD && $validCD->format('Y-m-d') === $checkInDate); // Check is a date.
            // dd($isCTValid);
            
            // Get check in time.
            $checkInTimeStr = substr($body, $checkInPos + $countDate + $countMonth + 7, 8); // Receiving format Y/d/m.
            $checkInTimeTrim = str_replace('<', '', $checkInTimeStr);
            $checkInTime = date('g:i:s A', strtotime($checkInTimeTrim)); // Convert to Y-m-d.
            $validCT = DateTime::createFromFormat('g:i:s A', $checkInTime);
            $isCTValid = ($validCT && $validCT->format('g:i:s A') === $checkInTime); // Check is a date.
            // dd($isCTValid);

            $emp = User::where('staff_id', trim(preg_replace('/[\(\)]/', '', $staffID)))->first();

            if (($emp != null) && ($clinicName != null) && ($isCDValid == true) && ($isCTValid == true))
            {
                $checkin = new HealthMetricsCheckin;
                $checkin->user_id = $emp->id;
                $checkin->clinic_name = $clinicName;
                $checkin->check_in_date = $checkInDate;
                $checkin->check_in_time = $checkInTime;
                $checkin->save();
            }
        };

    }
}
