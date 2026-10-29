<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'trust_score',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trust_score' => 'float',
        ];
    }

    /**
     * Cek otorisasi akses ke panel Filament Admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relasi ke Items yang dilaporkan user (lost/found).
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relasi ke Pesan Tiket Klaim yang dikirim oleh user.
     */
    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    /**
     * Relasi ke laporan yang dibuat oleh user ini.
     */
    public function filedReports(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    /**
     * Relasi ke laporan yang ditujukan terhadap user ini.
     */
    public function receivedReports(): HasMany
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
    }

    /**
     * Cek apakah user memiliki peran admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
