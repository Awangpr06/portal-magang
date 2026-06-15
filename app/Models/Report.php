<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'judul',
        'jenis',
        'periode',
        'durasi_jam',
        'file',
        'status',
        'catatan_mentor',
        'catatan_pembimbing',
        'pembimbing_review_status',
        'catatan',
        'reviewer_id',
        'admin_approved_at',
    ];

    protected $casts = [
        'admin_approved_at' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
