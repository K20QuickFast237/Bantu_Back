<?php

namespace App\Mail;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureRejected extends Mailable
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
            subject: 'Candidature Refusée',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $nom = $this->candidature->particulier->user->nom . ' ' . $this->candidature->particulier->user->prenom;
        return new Content(
            view: 'mails.candidature.rejected',
            with: [
                'recipientName' => $nom,
                'positionTitle' => $this->candidature->offre->titre_poste,
                'applicantName' => $nom,
                'applicantEmail' => $this->candidature->particulier->user->email,
                'submittedAt' => $this->candidature->created_at->format('d/m/Y'),
                'companyName' => env('APP_NAME', 'BantuLink'),
                'messageBody' => $this->candidature->message ?? null,
                'supportEmail' => env('SUPPORT_EMAIL', 'support@bantulink.tech'),
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
