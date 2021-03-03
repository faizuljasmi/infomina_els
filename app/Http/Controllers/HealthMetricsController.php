<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Webklex\IMAP\Facades\Client;
use App\Notifications\HealthMetricsUpdate;
use App\Notifications\HealthMetricsHRUpdate;
use App\User;
use App\HealthMetric;
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
        $medical_certs = HealthMetric::orderBy('id', 'DESC')->paginate(15);

        return view('admin.healthmetrics')->with(compact('medical_certs'));
    }

    public function search(Request $request) {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        $query = HealthMetric::query();
        
        if ($date_from != null && $date_to != null) {
            $query->whereBetween('leave_from', [$date_from, $date_to])
                  ->orWhereBetween('leave_to', [$date_from, $date_to]);
        }
         
        $medical_certs = $query->paginate(15);

        return view('admin.healthmetrics')->with(compact('medical_certs'));
    }

    public function fetch() {
        $client = Client::account('gmail'); // Setup imap.php
        $client->connect();
        
        $inbox = $client->getFolder('INBOX');
    
        $dateToday = date('d.m.Y');
        
        // $mails = $inbox->messages()->unanswered()->since('02.03.2020')->subject('HMS medical certificate issued')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('HMS medical certificate issued')->get();
        $mails = $inbox->messages()->since('02.03.2021')->subject('HMS medical certificate issued')->get();
        
        foreach($mails as $mail){
            $body = $mail->getHTMLBody();
            
            (date('j') < 10) ? $countDate = 1 : $countDate = 2; 
            (date('n') < 10) ? $countMonth = 1 : $countMonth = 2;
            
            // Find employee ID.
            $staffIDPos = strpos($body, ': IF');
            $staffID = substr($body, $staffIDPos +2, 6);
            // dd($staffID);
            
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

                    $takenLeave = TakenLeave::where('user_id', $emp->id)->where('leave_type_id', 3)->first();
                    $takenLeave->no_of_days = $takenLeave->no_of_days + intval($totalDays);
                    $takenLeave->update();
    
                    $hm = new HealthMetric;
                    $hm->user_id = $emp->id;
                    $hm->application_id = $leaveApp->id;
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
                
                // $emp->notify(new HealthMetricsUpdate($healthUpdate));

                foreach($admins as $admin) {
                    $healthUpdateHR = [
                        'hr_name' => $admin->name,
                        'name' => $emp->name,
                        'date_from' => $leaveFrom, 
                        'date_to' => $leaveTo, 
                        'total_days' => $totalDays, 
                        'status' => 'success',
                    ];
                    
                    // $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
                }
            } else {
                foreach($admins as $admin) {
                    $healthUpdateHR = [
                        'hr_name' => $admin->name,
                        'mail' => $body,
                        'status' => 'fail',
                    ];

                    // $admin->notify(new HealthMetricsHRUpdate($healthUpdateHR));
                }
            }
            
            // $mail->setFlag('answered');

        }
        
        return response()->json(['success'=>'Mail fetched successfully!']);
    }

    public function revert(Request $request){
        $application_id = $request->get('application_id');

        $la = LeaveApplication::where('id', $application_id)->first();
        $la->status = 'CANCELLED';
        $la->remarker_id = auth()->user()->id;

        $hm = HealthMetric::where('application_id', $application_id)->first();
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
}
