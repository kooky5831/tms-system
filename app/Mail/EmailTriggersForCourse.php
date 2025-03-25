<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class EmailTriggersForCourse extends Mailable
{
    use Queueable, SerializesModels;

    protected $mediaAttachment;

    /**
     * @var Email Content
    */
    private $emailContent;

    /**
     * @var Student Name
    */
    private $studentName;

    /**
     * @var Course
    */
    private $course;

    /**
     * @var Course Name
    */
    private $courseName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailContent, $mediaAttachment = null)
    // public function __construct($emailContent, $studentName, $course, $courseName)
    {
        $this->emailContent = $emailContent;
        $this->mediaAttachment = $mediaAttachment;
        // $this->studentName  = $studentName;
        // $this->course       = $course;
        // $this->courseName   = $courseName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $_html = $this->emailContent;
        $mymail = $this->subject('Course Reminder')
                    ->html($_html)
                    // ->markdown($this->templateSlug)
                    /*->with([
                        'studentName'   => $this->studentName,
                        'courseDate'    => $this->course->course_start_date,
                        'courseName'    => $this->courseName,
                    ])*/;
        if( $this->mediaAttachment ) {
            $mymail->attach($this->mediaAttachment);
        }
        return $mymail;
    }
}
