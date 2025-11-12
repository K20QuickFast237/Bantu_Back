<?php

namespace App\Mail;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureSended extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Candidature $candidature,
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Candidature Sended',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.candidature.sended',
            with: [
                'recipientName' => '', //$this->candidature->name,
                'positionTitle' => $this->candidature->offre->titre_poste,
                'applicantName' => auth()->user()->nom . ' ' . auth()->user()->prenom,
                'applicantEmail' => auth()->user()->email,
                'submittedAt' => $this->candidature->created_at->format('d/m/Y'),
                'companyName' => env('APP_NAME', 'BantuLink'),
                // 'actionUrl' => '',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
