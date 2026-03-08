<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicantMessage extends Notification
{
    use Queueable;

    protected $applicant;
    protected $message;
    protected $jobTitle;
    protected $attachmentPath;
    protected $attachmentName;

    /**
     * Create a new notification instance.
     */
    public function __construct($applicant, $message, $jobTitle = null, $attachmentPath = null, $attachmentName = null)
    {
        $this->applicant = $applicant;
        $this->message = $message;
        $this->jobTitle = $jobTitle;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $jobContext = $this->jobTitle ? " regarding their application for {$this->jobTitle}" : "";
        $mail = (new MailMessage)
            ->subject("New Inquiry from Applicant: {$this->applicant->name}")
            ->greeting("Hello Team,")
            ->line("An applicant, **{$this->applicant->name}**, has sent a message through the Hiring Hub portal{$jobContext}.")
            ->line("**Message:**")
            ->line($this->message);

        if ($this->attachmentPath) {
            $mail->line("**An attachment was provided with this message.**");
        }

        return $mail->action('View Applicant Profile', url('/admin/applicants/' . $this->applicant->id))
            ->line('Please follow up with the applicant as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'applicant_message',
            'title' => "Inquiry from {$this->applicant->name}",
            'message' => $this->message,
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->name,
            'applicant_email' => $this->applicant->email,
            'job_title' => $this->jobTitle,
            'attachment_path' => $this->attachmentPath,
            'attachment_name' => $this->attachmentName,
        ];
    }
}
