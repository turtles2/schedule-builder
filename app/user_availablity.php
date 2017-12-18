<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_availablity extends Model
{
    protected $table = 'user_availablity';

    protected $fillable = [
        'available', 'starts', 'ends','user_schedule_template'
    ];

    public function schedule_template()
    {
        return $this->belongsTo('App\user_schedule_template', 'user_schedule_template', 'id');
    }
}
