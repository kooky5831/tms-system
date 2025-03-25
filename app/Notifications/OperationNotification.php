<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;

class OperationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notifyData)
    {
        $this->name = $notifyData['name'];
        $this->start_date = $notifyData['start_date'];
        $this->end_date = $notifyData['end_date'];
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
        
        $notify_info = ":bell: *No registrations* for: " .$this->name. " from " .$this->start_date." to ".$this->end_date." :bell:";
        $notify_info1 = "There are no signups for clinic session date. Please check if there any last minute signups and inform trainer of cancellation if there are none";
        
       return (new SlackMessage)
            ->block(function ($block) {
                $block->type('divider');
            })
            ->block(function ($block) use ($notify_info) {
                $block->type('section')
                    ->text([
                        'type' => 'mrkdwn',
                        'text' => $notify_info
                    ]);
            })
            ->block(function ($block) {
                $block->type('divider');
            })
            ->block(function ($block) use ($notify_info1) {
                $block->type('section')
                    ->text([
                        'type' => 'mrkdwn',
                        'text' => $notify_info1
                    ]);
            });
                
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
