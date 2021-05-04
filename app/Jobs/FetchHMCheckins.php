<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client;
use App\User;
use App\HealthMetricsCheckin;
use Notification;
use DateTime;

class FetchHMCheckins implements ShouldQueue
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

        // $mails = $inbox->messages()->unanswered()->since('22.04.2021')->subject('Employee check-in with HMS')->get();
        $mails = $inbox->messages()->unanswered()->since($dateToday)->subject('Employee check-in with HMS')->get();
        // $mails = $inbox->messages()->since($dateToday)->subject('Employee check-in with HMS')->get();

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

                $mail->setFlag('answered');
            }
        };
    }
}
