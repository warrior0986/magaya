<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeatherRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'region_name', 'current_conditions', 'status'];

    protected $casts = [
        'current_conditions' => 'array',
    ];

    public function comments()
    {
        return $this->morphMany('App\Models\Comments', 'commentable');
    }
}
