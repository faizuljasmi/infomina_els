<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HealthMetricsHRUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($healthUpdateHR)
    {
        $this->healthUpdateHR = $healthUpdateHR;
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
        $healthUpdateHR = $this->healthUpdateHR;

        $message = new MailMessage;
        $message->greeting('Hi '.$healthUpdateHR['hr_name'].',');

        if ($healthUpdateHR['status'] == 'success') {
            $message->subject('[INFOMINA ELS] - Medical Leave Auto Applied');
            $message->line('New medical certificate has been issued by Health Metrics.');
            $message->line('Employee Name : '.$healthUpdateHR['name']);
            $message->line('Date : '.$healthUpdateHR['date_from'].' to '.$healthUpdateHR['date_to']);
            $message->line('Total Day(s) : '.$healthUpdateHR['total_days']);
        } else if ($healthUpdateHR['status'] == 'fail') {
            $message->subject('[INFOMINA ELS] - Medical Leave Apply Failed');
            $message->line('It seems like there are some mismatched details from the below mail.');
            $message->line($healthUpdateHR['mail']);
            $message->line('Kindly process the leave application manually.');
        }
        
        $message->line('Thank you!');

        return $message;
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
