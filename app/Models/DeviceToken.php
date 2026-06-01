<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'player_id', 
        'fcm_token', 
        'is_notification_enabled', 
        'created_at', 
        'updated_at'
    ];
}
