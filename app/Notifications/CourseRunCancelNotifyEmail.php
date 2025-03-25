<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class CourseRunCancelNotifyEmail extends Notification
{
    use Queueable;

    /*
    * CourseRun Id
    */
    protected $courseRunId;

    /*
    * CourseName
    */
    protected $courseRunName;

    /*
    * TPGateway Id
    */
    protected $tpGatewayId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($rid, $rname, $tpid, $msg)
    {
        $this->courseRunId = $rid;
        $this->courseRunName = $rname;
        $this->tpGatewayId = $tpid;
        $this->message = $msg;
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
        return (new MailMessage)
                    ->subject('Course Run Cancel Notification - TMS')
                    ->greeting('Hello Admin!')
                    ->line('Please take Note - '.$this->message)
                    ->line('CourseRun Id: '.$this->courseRunId)
                    ->line('Course Run Title: '.$this->courseRunName)
                    ->line('TPGateway Id: '.$this->tpGatewayId)                    
                    ->line('Thank you for choosing TMS');
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
