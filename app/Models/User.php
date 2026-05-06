<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'gender',
        'target_gender',
        'location',
        'ip_address',
        'is_guest',
        'is_online',
        'session_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_guest' => 'boolean',
            'is_online' => 'boolean',
        ];
    }

    public function chatSessionsAsUser1(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'user1_id');
    }

    public function chatSessionsAsUser2(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'user2_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function isLookingFor(string $gender): bool
    {
        return $this->target_gender === 'any' || $this->target_gender === 'both' || $this->target_gender === $gender;
    }

    public function getDisplayName(): string
    {
        return $this->is_guest ? 'Anonymous' : ($this->name ?? 'Anonymous');
    }

    public function getLocationAttribute(): string
    {
        return $this->attributes['location'] ?? 'Unknown';
    }
}