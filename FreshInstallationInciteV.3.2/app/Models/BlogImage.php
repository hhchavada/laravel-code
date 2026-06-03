<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',   // Add the 'image' field
        'blog_id',
        'created_at',
        'updated_at',
    ];
}
