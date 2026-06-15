<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'mentor_id',
        'pembimbing_id',
        'jenis',
        'periode',
        'komponen',
        'bobot',
        'nilai',
        'nilai_akhir',
        'status',
        'catatan',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'nilai' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
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
