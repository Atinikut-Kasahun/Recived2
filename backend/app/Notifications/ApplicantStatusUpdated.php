<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicantStatusUpdated extends Notification
{
    use Queueable;

    protected $applicant;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct($applicant, $oldStatus, $newStatus)
    {
        $this->applicant = $applicant;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'written_exam' => 'Written Exam Stage',
            'technical_interview' => 'Technical Interview Stage',
            'final_interview' => 'Final Interview Stage',
            'offer' => 'Job Offer',
            'hired' => 'Hired 🎉',
            'rejected' => 'Application Update'
        ];

        $status = $statusLabels[$this->newStatus] ?? 'Application Update';
        $jobTitle = $this->applicant->jobPosting->title ?? 'Position';

        $mail = (new MailMessage)
            ->subject("Important Update: Your Application for {$jobTitle}")
            ->greeting("Hello {$this->applicant->name},");

        if ($this->newStatus === 'written_exam') {
            $mail->line("Congratulations! You have been shortlisted for the **Written Exam** for the {$jobTitle} position.")
                ->line("Please log in to your dashboard for instructions and to track your progress.");
        } elseif ($this->newStatus === 'technical_interview') {
            $mail->line("Great news! You have advanced to the **Technical Interview** stage for {$jobTitle}.")
                ->line("Our team will reach out to schedule a suitable time, or keep an eye on your dashboard for updates.");
        } elseif ($this->newStatus === 'final_interview') {
            $mail->line("You've reached the **Final Interview** stage! This is an important step in our hiring process.")
                ->line("We are impressed with your profile and look forward to the next conversation.");
        } elseif ($this->newStatus === 'offer') {
            $mail->line("We are excited to extend an **Offer** to you for the {$jobTitle} position!")
                ->line("Welcome to the team (preliminary)! Please check your dashboard for details.");
        } elseif ($this->newStatus === 'rejected') {
            $mail->line("Thank you for your interest in the {$jobTitle} position.")
                ->line("After careful review, we've decided to move forward with other candidates at this time.")
                ->line("We wish you the best in your career pursuits.");
        } else {
            $mail->line("There has been an update on your application for the **{$jobTitle}** position.")
                ->line("Your status has been updated to: **{$status}**.");
        }

        if ($this->applicant->written_exam_score && $this->newStatus === 'written_exam') {
            $mail->line("Your written exam score: **{$this->applicant->written_exam_score}%**");
        }

        if ($this->applicant->technical_interview_score && $this->newStatus === 'technical_interview') {
            $mail->line("Your technical interview score: **{$this->applicant->technical_interview_score}%**");
        }

        return $mail
            ->action('View My Dashboard', url('/my-applications'))
            ->line('Droga Group Talent Acquisition Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->applicant->jobPosting->title ?? 'Position';
        $message = "Your status for {$jobTitle} has been updated to " . ($this->newStatus);

        if ($this->newStatus === 'written_exam')
            $message = "Shortlisted for Written Exam for {$jobTitle}!";
        if ($this->newStatus === 'technical_interview')
            $message = "Invited to Technical Interview for {$jobTitle}!";
        if ($this->newStatus === 'final_interview')
            $message = "Advanced to Final Interview for {$jobTitle}!";
        if ($this->newStatus === 'offer')
            $message = "You received an Offer for {$jobTitle}!";
        if ($this->newStatus === 'hired')
            $message = "Congratulations! You are officially HIRED for {$jobTitle}!";
        if ($this->newStatus === 'rejected')
            $message = "Decision update regarding your application for {$jobTitle}.";

        return [
            'title' => 'Application Update',
            'message' => $message,
            'status' => $this->newStatus,
            'job_title' => $jobTitle,
            'written_exam_score' => $this->applicant->written_exam_score,
            'technical_interview_score' => $this->applicant->technical_interview_score,
            'interviewer_feedback' => $this->applicant->interviewer_feedback,
            'applicant_id' => $this->applicant->id,
            'type' => 'status_update'
        ];
    }
}
