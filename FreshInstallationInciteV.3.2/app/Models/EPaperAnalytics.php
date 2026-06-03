<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPaperAnalytics extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    protected $table = "e_paper_analytics";

    public function epaper()
    {
        return $this->belongsTo(EPaper::class, 'e_paper_id', 'id');
    }


}
