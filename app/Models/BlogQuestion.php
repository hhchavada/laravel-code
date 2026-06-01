<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogQuestion extends Model
{
    use HasFactory; 
    protected $table = "blog_pool_questions";
    
    
    public function options()
    {
        return $this->hasMany(BlogQuestionOption::class, 'blog_pool_question_id');
    }
}
