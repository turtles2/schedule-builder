<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class schedule_period extends Model
{
    protected $table = 'schedule_period';

    protected $fillable = [
        'max_shift', 'min_shift', 'preferred_shift','starts','ends'
    ];

    public function template()
    {
        return $this->hasMany('App\schedule_template', 'schedule_period', 'id');
    }

    public function user_templates()
    {
        return $this->hasMany('App\user_schedule_template', 'schedule_period', 'id');
    }

    public function setstartsAttribute($value)
   {

       $value = Carbon::parse($value);

       $value = $value->toDateString();

       $this->attributes['starts'] = $value;
   }

   public function setendsAttribute($value)
   {
       $value = Carbon::parse($value);

       $value = $value->toDateString();

       $this->attributes['ends'] = $value;
   }
}
