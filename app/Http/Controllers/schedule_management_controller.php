<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\schedule_template;

use App\schedule_period;

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
        'timezone' => config('app.timezone'),
        ])->
        setCallbacks([]);

        return view('schedule_management.new',compact('calendar'));
    }

    public function new_schedule_store(Request $request)
    {
        // validate input
        $validatedData = $request->validate([
           'max' => 'required|min:0|numeric',
           'min' => 'required|min:0|numeric',
           'preferred' => 'required|min:0|numeric',
           'start' => 'required|date',
           'end' => 'required|date|after:start',
           'shifts' => 'present',
        ]);

        $output = str_getcsv($validatedData['shifts']);

        $shifts = array();

        $shift_data = array();

        $row_count = 1;
        // parse data from full calendar
        foreach ($output as $row) {

            switch ($row_count) {
                case 1:
                    preg_match('/(\d{1,})\s/', $row, $matches);
                    $shift_data['employees'] = $matches[1];
                    $row_count++;
                    break;
                case 2:
                    $shift_data['start'] = $row;
                    $row_count++;
                    break;
                case 3:
                    $shift_data['end'] = $row;
                    array_push($shifts,$shift_data);
                    $shift_data = array();
                    $row_count = 1;
                    break;
            }
        }
        // parse datetimes
        foreach ($shifts as $key => $shift)
        {
            $start = Carbon::parse($shift['start']);

            if($shift['end'] == null)
            {
                $end = $start->copy();

                $end = $end->addHours(2);

            }else {
                $end = Carbon::parse($shift['end']);
            }

            $shifts[$key]['start'] = $start;
            $shifts[$key]['end'] = $end;
        }

        // create model
        $period = new schedule_period;
        // add data to model
        $period->max_shift = $validatedData['max'];
        $period->min_shift = $validatedData['min'];
        $period->preferred_shift = $validatedData['preferred'];
        $period->starts = $validatedData['start'];
        $period->ends = $validatedData['end'];
        // save model
        $period->save();

        $period_id = $period->id;

        foreach ($shifts as $key => $shift) {

            // create model
            $shift_template = new schedule_template;
            // add data to model
            $shift_template->employees = $shift['employees'];
            $shift_template->starts = $shift['start'];
            $shift_template->ends = $shift['end'];
            $shift_template->schedule_period = $period_id;
            // save model
            $shift_template->save();
        }

        return redirect('/');

    }
}
