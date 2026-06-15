<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'tanggal',
        'tanggal_selesai',
        'jam_masuk',
        'jam_pulang',
        'status',
        'durasi_menit',
        'keterangan',
        'lampiran',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
