<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class blogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'bio',
        'background',
    ];
}
