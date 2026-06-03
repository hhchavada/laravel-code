<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortVideoAnalytic extends Model
{
    use HasFactory;

    public function user(){
        return $this->hasOne('App\Models\User',"id","user_id");
    }

    public function shortVideo()
    {
        return $this->belongsTo(ShortVideo::class, 'short_video_id', 'id');
    }

}
