<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public Applicant $applicant;
    public string $oldStatus;
    public string $newStatus;
    public string $jobTitle;
    public string $companyName;
    public ?string $offerLetterPath;
    public ?string $examPaperPath;
    public ?float $score;
    public ?\App\Models\Interview $interview;
    public ?string $interviewMessage;

    public function __construct(
        Applicant $applicant,
        string $oldStatus,
        string $newStatus,
        ?string $offerLetterPath = null,
        ?\App\Models\Interview $interview = null,
        ?string $interviewMessage = null,
        ?string $examPaperPath = null
    ) {
        $this->applicant = $applicant;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->jobTitle = $applicant->jobPosting->title ?? 'the position';
        $this->companyName = $applicant->tenant->name ?? 'Our Company';
        $this->offerLetterPath = $offerLetterPath;
        $this->examPaperPath = $examPaperPath;
        $this->interviewMessage = $interviewMessage;

        // Define which score corresponds to which status
        // If moving TO technical interview, show Written Exam result
        if ($newStatus === 'technical_interview' || ($newStatus === 'written_exam' && $applicant->written_exam_score !== null)) {
            $this->score = $applicant->written_exam_score;
        }
        // If moving TO final interview, show Technical result
        elseif ($newStatus === 'final_interview' || ($newStatus === 'technical_interview' && $applicant->technical_interview_score !== null)) {
            $this->score = $applicant->technical_interview_score;
        } else {
            $this->score = null;
        }

        if ($interview) {
            $this->interview = $interview;
        } else {
            // Find the most recent scheduled interview for this applicant
            // logic to match stages to interview types
            $typeMap = [
                'written_exam' => 'written_exam',
                'technical_interview' => 'technical',
                'final_interview' => 'final',
                'offer' => 'offer_meeting',
            ];

            $type = $typeMap[$newStatus] ?? null;

            if ($type) {
                $this->interview = \App\Models\Interview::where('applicant_id', $applicant->id)
                    ->where('type', $type)
                    ->orderBy('created_at', 'desc')
                    ->first();
            } else {
                $this->interview = null;
            }
        }
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'written_exam' => "📝 Next Step: Written Exam — {$this->jobTitle}",
            'technical_interview' => "🛠️ Technical Interview Invitation — {$this->jobTitle}",
            'final_interview' => "🎯 Final Interview Invitation — {$this->jobTitle}",
            'offer' => "🎉 Congratulations! Job Offer — {$this->jobTitle}",
            'hired' => "✅ Welcome Aboard! — {$this->companyName}",
            'rejected' => "Update on Your Application — {$this->jobTitle}",
        ];

        $subject = $subjects[$this->newStatus]
            ?? "Application Update: {$this->jobTitle} at {$this->companyName}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.status-changed');
    }

    /**
     * Attach the offer-letter PDF when present (offer stage only).
     */
    public function attachments(): array
    {
        $attachments = [];

        // 1. Offer-letter attachment
        if ($this->newStatus === 'offer' && $this->offerLetterPath && file_exists($this->offerLetterPath)) {
            $ext = pathinfo($this->offerLetterPath, PATHINFO_EXTENSION);
            $attachments[] = Attachment::fromPath($this->offerLetterPath)
                ->as('Official_Offer_Letter_' . preg_replace('/[^A-Za-z0-9_]/', '_', $this->applicant->name) . '.' . $ext);
        }

        // 2. Exam Paper attachment
        if ($this->examPaperPath && file_exists($this->examPaperPath)) {
            $attachments[] = Attachment::fromPath($this->examPaperPath)
                ->as('Exam_Result_Paper_' . preg_replace('/[^A-Za-z0-9_]/', '_', $this->applicant->name) . '.' . pathinfo($this->examPaperPath, PATHINFO_EXTENSION));
        }

        return $attachments;
    }
}
