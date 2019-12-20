<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $la = $this->leaveApplication;

        if($la->status == 'PENDING_1'|| $la->status == 'PENDING_2'|| $la->status == 'PENDING_3' ){

            if($la->status == 'PENDING_1'){
                $currAuth = $la->approver_one->name;
            }
            else if($la->status == 'PENDING_2'){
                $currAuth = $la->approver_two->name;
            }
            else if($la->status == 'PENDING_3'){
                $currAuth = $la->approver_three->name;
            }
            $subject = 'Leave Application Status Update';
            $desc = 'Your leave application status has been updated';
            $stat = 'Waiting approval by '.$currAuth;  
        }
        else if($la->status == 'DENIED_1'|| $la->status == 'DENIED_2'|| $la->status == 'DENIED_3' ){
            
            if($la->status == 'DENIED_1'){
                $currAuth = $la->approver_one->name;
            }
            else if($la->status == 'DENIED_2'){
                $currAuth = $la->approver_two->name;
            }
            else if($la->status == 'DENIED_3'){
                $currAuth = $la->approver_three->name;
            }
            $subject = 'Leave Application Denied';
            $desc = 'Your leave application has been denied';
            $stat = 'Denied by '.$currAuth;
        }
        else{
            $subject = 'Leave Application Approved';
            $desc = 'Your leave application has been approved';
            $stat = 'Approved';
        }

        return (new MailMessage)
        ->subject('[INFOMINA ELS] '.$subject)
        ->greeting('Hi,'.$la->user->name)
        ->line($desc)
        ->line('Status: '.$stat)
        ->line('Leave type: '.$la->leaveType->name)
        ->line('From: '.$la->date_from)
        ->line('To: '.$la->date_to)
        ->line('Total day(s): '.$la->total_days)
        ->line('Resume date: '.$la->date_resume)
        ->line('Reason: '.$la->reason)
        ->line('Relief Personnel: '.$la->relief_personnel->name)
        ->line('Emergency Contact: '.$la->emergency_contact)
        // ->action('View application', $url)
        ->line('Have a nice day!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
