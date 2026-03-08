<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DirectMessage extends Notification
{
    use Queueable;

    protected string $senderName;
    protected int $senderId;
    protected string $message;
    protected ?string $candidateName;
    protected ?int $applicantId;
    protected ?string $attachmentPath;
    protected ?string $attachmentName;

    public function __construct(string $senderName, int $senderId, string $message, ?string $candidateName = null, ?int $applicantId = null, ?string $attachmentPath = null, ?string $attachmentName = null)
    {
        $this->senderName = $senderName;
        $this->senderId = $senderId;
        $this->message = $message;
        $this->candidateName = $candidateName;
        $this->applicantId = $applicantId;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mail = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("New message from Droga Recruitment Team")
            ->greeting("Hello,")
            ->line("You have received a new message regarding your application.")
            ->line("**Message:**")
            ->line($this->message);

        if ($this->attachmentPath) {
            $mail->line("**An attachment has been sent with this message.**");
        }

        return $mail->action('View in Hiring Hub', url('/my-applications'))
            ->line('Thank you for your interest in Droga Group!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'mention',
            'title' => "Message from {$this->senderName}",
            'message' => $this->message,
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'candidate_name' => $this->candidateName,
            'applicant_id' => $this->applicantId,
            'attachment_path' => $this->attachmentPath,
            'attachment_name' => $this->attachmentName,
        ];
    }
}
