<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_schedule_template extends Model
{
    protected $table = 'user_schedule_template';

    protected $fillable = [
        'shift_max', 'preferred', 'weekly_max','user_id','schedule_period'
    ];

    public function availability()
    {
        return $this->hasMany('App\user_availablity', 'user_schedule_template', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
