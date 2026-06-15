<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'foto',
        'phone',
        'address',
        'account_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'two_factor_enabled',
        'password_changed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function peserta()
    {
        return $this->hasOne(Peserta::class);
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class);
    }

    public function pembimbing()
    {
        return $this->hasOne(Pembimbing::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function authoredAnnouncements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class)
            ->withPivot(['dibaca_pada', 'disimpan_pada'])
            ->withTimestamps();
    }

    public function securityActivities()
    {
        return $this->hasMany(SecurityActivity::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verificationHistories()
    {
        return $this->hasMany(VerificationHistory::class);
    }

    public function handledVerificationHistories()
    {
        return $this->hasMany(VerificationHistory::class, 'admin_id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }
}
