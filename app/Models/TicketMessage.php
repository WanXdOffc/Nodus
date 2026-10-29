<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    /**
     * Tiket klaim tempat pesan ini dikirim.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ClaimTicket::class, 'ticket_id');
    }

    /**
     * User pengirim pesan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
