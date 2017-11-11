<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class schedule_management_controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function new_schedule()
    {
        $calendar = \Calendar::setOptions([
        'firstDay' => 0,
        'editable' => true,
        'droppable' => true,
        'dragRevertDuration' => 0,
        'defaultView' => 'agendaWeek',
        ])->
        setCallbacks([]);

        return view('schedule_management.new',compact('calendar'));
    }

    public function new_schedule_store(Request $request)
    {
        return $request;
    }
}
