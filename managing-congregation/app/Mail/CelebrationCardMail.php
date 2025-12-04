<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CelebrationCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Member $member,
        public string $imageContent,
        public string $subjectLine
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.celebration-card',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->imageContent, 'celebration-card.png')
                ->withMime('image/png'),
        ];
    }
}
