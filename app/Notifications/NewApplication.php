<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplication extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leaveApp)
    {
        $this->leaveApp = $leaveApp;
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
        $la = $this->leaveApp;
        $url = url('/leave/apply/view/'.$la->id);
        //dd($la->user->name);
        return (new MailMessage)
                    ->subject('[INFOMINA ELS] New Leave Application- '.$la->user->name)
                    ->greeting('Hi,')
                    ->line('Leave application by '.$la->user->name.' is waiting for your approval:')
                    ->line('Leave type: '.$la->leaveType->name)
                    ->line('From: '.$la->date_from)
                    ->line('To: '.$la->date_to)
                    ->line('Total day(s): '.$la->total_days)
                    ->line('Resume date: '.$la->date_resume)
                    ->line('Reason: '.$la->reason)
                    ->line('Relief Personnel: '.$la->relief_personnel->name)
                    ->line('Emergency Contact: '.$la->emergency_contact)
                    ->action('View application', $url)
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
