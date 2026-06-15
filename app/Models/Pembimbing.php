<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembimbing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nidn_nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'instansi',
        'jabatan',
        'no_hp',
        'alamat',
        'perguruan_tinggi',
        'program_studi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
