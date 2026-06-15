<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pesan',
        'laporan',
        'penugasan',
        'absensi',
        'pengumuman',
        'email',
    ];

    protected $casts = [
        'pesan' => 'boolean',
        'laporan' => 'boolean',
        'penugasan' => 'boolean',
        'absensi' => 'boolean',
        'pengumuman' => 'boolean',
        'email' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
