<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HealthMetricsUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($healthUpdate)
    {
        $this->healthUpdate = $healthUpdate;
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
        $healthUpdate = $this->healthUpdate;
        
        $message = new MailMessage;
        $message->subject('[INFOMINA ELS] - Medical Leave Applied');
        $message->greeting('Hi '.$healthUpdate['name'].',');
        $message->line('It seems like you are unwell. Thus, we have submitted a medical leave on behalf of you.');
        $message->line('Date : '.$healthUpdate['date_from'].' to '.$healthUpdate['date_to']);
        $message->line('Total Day(s) : '.$healthUpdate['total_days']);
        $message->line('Please contact the HR for any changes in action.');
        $message->line('Take care!');

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
