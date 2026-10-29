<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClaimTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'ai_match_score',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ai_match_score' => 'integer',
        ];
    }

    /**
     * Item barang yang hilang.
     */
    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'lost_item_id');
    }

    /**
     * Item barang yang ditemukan.
     */
    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'found_item_id');
    }

    /**
     * Riwayat pesan percakapan dalam tiket ini.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }
}
