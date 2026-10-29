<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'category',
        'latitude',
        'longitude',
        'location_name',
        'item_date',
        'status',
        'image_path',
        'ai_tags',
    ];

    protected function casts(): array
    {
        return [
            'item_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
            'ai_tags' => 'array',
        ];
    }

    /**
     * User yang melaporkan item ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tiket klaim jika item ini berperan sebagai barang hilang (lost item).
     */
    public function lostClaimTickets(): HasMany
    {
        return $this->hasMany(ClaimTicket::class, 'lost_item_id');
    }

    /**
     * Tiket klaim jika item ini berperan sebagai barang temuan (found item).
     */
    public function foundClaimTickets(): HasMany
    {
        return $this->hasMany(ClaimTicket::class, 'found_item_id');
    }
}
