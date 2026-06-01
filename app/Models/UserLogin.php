<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'login_date_time', 
        'logout_date_time', 
        'created_at', 
        'updated_at'
    ];
}
