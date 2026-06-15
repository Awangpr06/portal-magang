<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'perguruan_tinggi_id',
    'nim',
    'tempat_lahir',
    'tanggal_lahir',
    'jenis_kelamin',
    'jurusan',
    'fakultas',
    'program_magang',
    'pembimbing_akademik',
    'tanggal_mulai_magang',
    'tanggal_selesai_magang',
    'semester',
    'no_hp',
    'alamat',
    'status'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_mulai_magang' => 'date',
        'tanggal_selesai_magang' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perguruanTinggi()
    {
        return $this->belongsTo(
            PerguruanTinggi::class
        );
    }

    public function internship()
    {
        return $this->hasOne(Internship::class)->latestOfMany();
    }

    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
