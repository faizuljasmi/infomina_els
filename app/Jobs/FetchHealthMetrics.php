<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\IMAP\Facades\Client;
use App\User;
use App\HealthMetric;

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
            $leaveFromStr = substr($body, $leaveFromPos + 10, $countDate + $countMonth + 7);
            $dFrom = explode('/',$leaveFromStr);
            $leaveFrom = date('Y-m-d', strtotime($dFrom[1].'/'.$dFrom[0].'/'.$dFrom[2]));
            
            // Find leave date to.
            $leaveToPos = strpos($body, '/'.date('Y').' to');
            $leaveToStr = substr($body, $leaveToPos + 9 , $countDate + $countMonth + 7);
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

        // return response()->json(['success' => 'Mail fetched!']);
    }
}
