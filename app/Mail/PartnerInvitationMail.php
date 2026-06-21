<?php

namespace App\Mail;

use App\Models\PartnerInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public PartnerInvitation $invitation;
    public string $acceptUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(PartnerInvitation $invitation)
    {
        $this->invitation = $invitation;
        $this->acceptUrl = url('/accept-invitation/' . $invitation->token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $partnerName = $this->invitation->partner?->name ?? 'YieldTech Partner';
        return new Envelope(
            subject: "Invitation to join {$partnerName} on YieldTech",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.partner_invitation',
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
