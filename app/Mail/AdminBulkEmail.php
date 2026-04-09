<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminBulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{path:string,name:string,mime?:string}>  $attachments
     */
    public function __construct(
        public string $subjectLine,
        public string $body,
        public array $fileAttachments = [],
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-bulk-email',
            with: [
                'body' => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        return array_map(function (array $attachment): Attachment {
            $file = Attachment::fromPath($attachment['path'])->as($attachment['name']);

            if (! empty($attachment['mime'])) {
                $file = $file->withMime($attachment['mime']);
            }

            return $file;
        }, $this->fileAttachments);
    }
}
