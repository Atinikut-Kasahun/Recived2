<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $applicantName;

    public function __construct(string $resetUrl, string $applicantName)
    {
        $this->resetUrl = $resetUrl;
        $this->applicantName = $applicantName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Your Password — Hiring Hub');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                // ✅ Pass as object so blade can use $applicant->name
                'applicant' => (object) ['name' => $this->applicantName],
                'resetUrl' => $this->resetUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
