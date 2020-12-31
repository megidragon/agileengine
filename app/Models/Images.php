<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $fillable = [
        'remote_id',
        'author',
        'camera',
        'tags',
        'cropped_picture',
        'full_picture',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
