<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id', 
        'type',
        'blog_id',
        'created_at',
        'updated_at',
    ];

    public function category(){
        return $this->hasOne('App\Models\Category',"id","category_id");
    }
}
