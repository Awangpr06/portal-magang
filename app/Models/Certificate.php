<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'nomor',
        'jenis',
        'periode',
        'predikat',
        'status',
        'tanggal_terbit',
        'file',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
