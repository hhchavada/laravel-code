<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogQuestionOption extends Model
{
    use HasFactory;
    protected $table = "blog_pool_options";
    
    
    // Define the votes relationship
    public function votes()
    {
        return $this->hasMany(Vote::class, 'option_id');
    }
}
