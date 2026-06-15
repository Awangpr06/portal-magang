<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'mentor_id',
        'judul',
        'deskripsi',
        'prioritas',
        'deadline',
        'status',
        'progress',
        'file_hasil',
        'file_pengumpulan',
        'submitted_at',
        'catatan',
    ];

    protected $casts = [
        'deadline' => 'date',
        'submitted_at' => 'datetime',
        'progress' => 'integer',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }
}
