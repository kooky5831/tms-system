<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Carbon;

class TrainerFolderCreationEmail extends Notification
{
    use Queueable;

    /*
    * User
    */
    protected $user;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $emailTemplate = EmailTemplate::where('slug', 'trainer-folder-on-google-drive-created-tms')->first();
        if (!empty($emailTemplate)) {
            $replacementArr['log_file_url'] = route('admin.download.log',['fname'=>'scheduler-'. Carbon::now()->toDateString().'.log']);
            $replacementArr['year'] = Carbon::now()->format('Y');
            $emailContent = replaceEmailContent($emailTemplate->template_text, $replacementArr);

            return (new MailMessage)->subject($emailTemplate->subject)->view('admin.emails.commonEmail', ['emailContent' => $emailContent]);
        }
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
