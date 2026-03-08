<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SentMessage extends Notification
{
    use Queueable;

    protected $message;
    protected $toTeam;
    protected $attachmentPath;
    protected $attachmentName;

    public function __construct($message, $toTeam, $attachmentPath = null, $attachmentName = null)
    {
        $this->message = $message;
        $this->toTeam = $toTeam;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sent_message',
            'title' => "Sent to {$this->toTeam}",
            'message' => $this->message,
            'is_sent' => true,
            'attachment_path' => $this->attachmentPath,
            'attachment_name' => $this->attachmentName,
        ];
    }
}
