<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class schedule_template extends Model
{
    protected $table = 'schedule_template';

    protected $fillable = [
        'employees', 'starts', 'ends','schedule_period'
    ];

    public function period()
    {
        return $this->belongsTo('App\schedule_period', 'schedule_period', 'id');
    }
}
