<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    // Ничего защищать не надо, для обновления и добавления атрибутов в бд
    protected $guarded = false;
    protected $table = 'images';
}
