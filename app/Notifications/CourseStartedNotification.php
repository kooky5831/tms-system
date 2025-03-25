<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NathanHeffley\LaravelSlackBlocks;
use NathanHeffley\LaravelSlackBlocks\Messages\SlackMessage;
use Illuminate\Support\Facades\Log;

class CourseStartedNotification extends Notification
{
    //use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
       return (new SlackMessage)
            ->block(function ($block) {
                $block->type('section')
                    ->text([
                        'type' => 'mrkdwn',
                        'text' => 'Please acknowledge once the class above has been started!'
                    ]);
            })
            ->block(function ($block) {
                $block->type('actions')
                    ->elements([[
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'Started?',
                        ],
                        'style' => 'danger',
                    ]]);
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
