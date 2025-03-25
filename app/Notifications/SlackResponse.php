<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use Illuminate\Support\Carbon;

class SlackResponse extends Notification
{
    //use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($response)
    {
        $json = json_decode($response['payload']);
        $this->user = $json->user->id;
    }  
        
    

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toSlack($notifiable)
    {
        $timeOutput = Carbon::now()->format('H:i:s');
        $reply = "<@" . $this->user . '> Thank you for starting the class above at ' . $timeOutput;
        return (new SlackMessage)
        ->content($reply);
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
