<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'mentor_id',
        'pembimbing_id',
        'instansi',
        'unit_kerja',
        'posisi',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'divisi',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class);
    }
}
