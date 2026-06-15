<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Peserta;

class PerguruanTinggi extends Model
{
    use HasFactory;

    protected $table = 'perguruan_tinggi';

    protected $fillable = [
        'nama_pt',
        'jenis',
        'status_kerja_sama',
        'pic',
        'pic_nip',
        'alamat',
        'telepon',
        'email',
        'fakultas',
        'program_studi',
        'logo',
    ];

    public function pesertas()
    {
        return $this->hasMany(Peserta::class, 'perguruan_tinggi_id');
    }
}
