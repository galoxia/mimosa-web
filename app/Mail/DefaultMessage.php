<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;

class DefaultMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private readonly User    $user,
        private readonly Message $message,
        private readonly array   $placeholders = []
    )
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address( $this->message->from, config( 'app.name' ) ),
            to: $this->user->email,
            cc: preg_split( '/\r\n|\r|\n/', trim( $this->message->cc ?? '' ) ),
            subject: $this->message->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.messages.default',
            with: [
                'content' => Blade::render(
                    $this->message->getParsedBody( $this->user, $this->placeholders )
                )
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $path = "messages/{$this->message->id}/attachments";

        return collect( $this->message->attachments )
            ->filter( fn( $filename ) => Storage::exists( "$path/$filename" ) )
            ->map( fn( $filename ) => Attachment::fromStorage( "$path/$filename" )
                ->as( $filename )
                ->withMime( Storage::mimeType( "$path/$filename" ) ?? 'application/octet-stream' )
            )
            ->all();
    }
}
