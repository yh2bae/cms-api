<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id', 'name', 'email', 'comment'
    ];

    public function getCreatedAtAttribute($created_at)
    {   
        $value = \Carbon\Carbon::parse($created_at);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }

    public function getUpdatedAtAttribute($updated_at)
    {   
        $value = \Carbon\Carbon::parse($updated_at);
        $parse = $value->locale('id');
        return $parse->translatedFormat('l, d F Y');
    }
}
