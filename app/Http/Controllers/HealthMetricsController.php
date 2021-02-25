<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use App\User;
use App\HealthMetric;

class HealthMetricsController extends Controller
{
    function index() {
        $medical_certs = HealthMetric::orderBy('id', 'DESC')->paginate(5);

        return view('admin.healthmetrics')->with(compact('medical_certs'));
    }

    function fetch() {
        $client = Client::account('gmail'); // Setup imap.php
        $client->connect();

        
        $inbox = $client->getFolder('INBOX');
        // dd($client->getFolders());
    
        // $dateToday = '24.02.2021';
        $dateToday = date('d.m.Y');
        
        $mails = $inbox->messages()->unanswered()->since($dateToday)->subject('HMS medical certificate issued')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('HMS medical certificate issued')->get();
        
        foreach($mails as $mail){
            // dd($mails);
            $body = $mail->getTextBody();
            
            (date('j') < 10) ? $countDate = 1 : $countDate = 2; 
            (date('n') < 10) ? $countMonth = 1 : $countMonth = 2;
            
            // Find employee ID.
            $empIDPos = strpos($body, ': IF');
            $empID = substr($body, $empIDPos +2, 6);
            
            // Find total days.
            $totalDaysPos = strpos($body, '-day');
            $totalDays = substr($body, $totalDaysPos -2, 2);
            
            // Find leave date from.
            $leaveFromPos = strpos($body, '(MC) from');
            $leaveFromStr = substr($body, $leaveFromPos + 10, $countDate + $countMonth + 6);
            $dFrom = explode('/',$leaveFromStr);
            $leaveFrom = date('Y-m-d', strtotime($dFrom[1].'/'.$dFrom[0].'/'.$dFrom[2]));
            
            // Find leave date to.
            $leaveToPos = strpos($body, '/'.date('Y').' to');
            $leaveToStr = substr($body, $leaveToPos + 9 , $countDate + $countMonth + 6);
            $dTo = explode('/',$leaveToStr);
            $leaveTo = date('Y-m-d', strtotime($dTo[1].'/'.$dTo[0].'/'.$dTo[2]));
            
            // Find MC link.
            $htmlLink = $mail->getHTMLBody();
            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $htmlLink, $result);
            if (!empty($result)) {
                $link = (!empty($result['href'][1])) ? $link = $result['href'][1] : $link = ''; // Got 2 links, the second one is the printable link.
            }

            // Get user using employee ID from mail.
            $emp = User::where('staff_id', trim(preg_replace('/[\(\)]/', '', $empID)))->first();
            
            $mc = new HealthMetric;
            $mc->user_id = $emp->id;
            $mc->leave_from = $leaveFrom;
            $mc->leave_to = $leaveTo;
            $mc->total_days = trim(preg_replace('/\s+/', '', $totalDays));
            $mc->link = $link;
            $mc->save();
            
            $mail->setFlag('answered');
        }

        return response()->json(['success' => 'Mail fetched!']);
    }

}
