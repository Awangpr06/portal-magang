<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_key',
        'name',
        'type',
        'status',
        'permissions',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}
