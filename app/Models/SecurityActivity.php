<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'aktivitas',
        'perangkat',
        'browser',
        'ip_address',
        'status',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
