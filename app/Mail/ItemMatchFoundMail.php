<?php

namespace App\Mail;

use App\Models\ClaimTicket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItemMatchFoundMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public ClaimTicket $ticket,
        public string $userRole // 'seeker' (loser) atau 'finder'
    ) {}

    public function envelope(): Envelope
    {
        $itemName = $this->ticket->lostItem?->title ?? $this->ticket->foundItem?->title ?? 'Barang Anda';

        return new Envelope(
            subject: "[NODUS] Kabar Baik! Kecocokan Barang Ditemukan: {$itemName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.item-matched',
            with: [
                'recipient' => $this->recipient,
                'ticket' => $this->ticket,
                'lostItem' => $this->ticket->lostItem,
                'foundItem' => $this->ticket->foundItem,
                'userRole' => $this->userRole,
                'actionUrl' => url('/messages'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
