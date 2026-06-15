<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'isi',
        'status',
        'tanggal',
        'lampiran',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function readers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['dibaca_pada', 'disimpan_pada'])
            ->withTimestamps();
    }
}
