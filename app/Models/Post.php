<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'bio',
        'background',
        'slug',
        'body',
        'photo_alt_text',
        'is_visible',
    ];
}
