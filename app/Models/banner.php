<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'background',
    ];
}
