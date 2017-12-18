<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\schedule_template;

use App\schedule_period;

use App\user_availablity;

use App\user_schedule_template;

class scheduling_controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function new_availability()
    {
        $schedules = schedule_period::where('starts', '>',Carbon::now()->toDateString())->get();

        $calendar = \Calendar::setOptions([
        'firstDay' => 0,
        'editable' => true,
        'droppable' => true,
        'dragRevertDuration' => 0,
        'defaultView' => 'agendaWeek',
        'timezone' => config('app.timezone'),
        ])->
        setCallbacks([]);


        return view('scheduling.new',compact('calendar','schedules'));
    }

    public function new_availability_template(Request $request)
    {
        $period = schedule_period::find(3);

        $time_window = array(
            'start' => Carbon::parse('2018-01-02T00:00:00')->timestamp,
            'end' => Carbon::parse('2018-01-03T00:00:00')->timestamp
        );

        $templates = $period->template()->where('starts','>=',$time_window['start'])
        ->orderBy('starts', 'asc')->get();

        $employee_time_needs = array();

        foreach ($templates as $template) {

            $times = array(
                "start" => Carbon::parse($template->starts)->timestamp,
                "end" =>    Carbon::parse($template->ends)->timestamp
            );

            array_push($employee_time_needs,$times);

        }

        $merged_times = array();

        $time_slot = array(
            'start' => null,
            'end' => null
        );

        foreach ($employee_time_needs as $key => $time_need) {

            if($time_slot['start'] === null)
            {
                // makes a new range slot
                $time_slot['start'] = $time_need['start'];
                $time_slot['end'] = $time_need['end'];
                continue;
            }

            if(($time_slot['start'] <= $time_need['start']) && (($time_need['start'] - 1) <= $time_slot['end']))
            {
                // sould merge ranges see examples below
                // ex1 range 1: 0 to 5 range 2: 3 to 7 new range: 0 to 7
                // ex2 range 1: 0 to 5 range 2: 5 to 9 new range: 0 to 9
                $time_slot['end'] = $time_need['end'];
                continue;
            }

            // push to time array
            array_push($merged_times,$time_slot);

            // reset time slot
            $time_slot['start'] = $time_need['start'];
            $time_slot['end'] = $time_need['end'];

        }
        // final push to get last value into array
        array_push($merged_times,$time_slot);

        $closed_times = array();
        $id = 0;
        // assumes $merged_times is sorted
        foreach ($merged_times as $key => $time) {

            if($time['start'] > $time_window['start'])
            {
                $closed_time = array(
                    'title' => 'Workers Not Needed',
                    'id' => $id++,
                    'start' => Carbon::createFromTimestamp($time_window['start'])->toW3cString(),
                    'end' => Carbon::createFromTimestamp($time['start'])->toW3cString()
                );

                array_push($closed_times,$closed_time);

            }

            $time_window['start'] = $time['end'];
        }

        $last_time = end($merged_times);

        if($last_time['end'] < $time_window['end'])
        {
            $closed_time = array(
                'title' => 'Workers Not Needed',
                'id' => $id++,
                'start' => Carbon::createFromTimestamp($last_time['end'])->toW3cString(),
                'end' => Carbon::createFromTimestamp($time_window['end'])->toW3cString()
            );

            array_push($closed_times,$closed_time);
        }
        // to do connvert times to better format?
        return response()->json(
            $closed_times
        );
    }

    public function new_availability_store(Request $request)
    {
        // validate input
        $validatedData = $request->validate([
           'max' => 'required|min:0|numeric',
           'preferred' => 'required|min:0|numeric',
           'hours' => 'required|min:0|numeric',
           'period' => 'required|numeric',
           'shifts' => 'present',
        ]);

        $output = str_getcsv($validatedData['shifts']);

        $availability = array();

        $availability_data = array();

        $row_count = 1;

        $skip = 0;
        // parse data from full calendar
        foreach ($output as $row) {
            if ($skip > 0) {
                $skip--;
                continue;
            }

            switch ($row_count) {
                case 1:
                    $search = strpos($row,"Workers Not Needed");
                    if($search === 0)
                    {
                        $skip = 2;
                        break;
                    }
                    $search = strpos($row,"Not");
                    if($search === false)
                    {
                        $availability_data['available'] = true;
                    }
                    else
                    {
                        $availability_data['available'] = false;
                    }
                    $row_count++;
                    break;
                case 2:
                    $availability_data['start'] = $row;
                    $row_count++;
                    break;
                case 3:
                    $availability_data['end'] = $row;
                    array_push($availability,$availability_data);
                    $availability_data = array();
                    $row_count = 1;
                    break;
            }
        }

        // parse datetimes
        foreach ($availability as $key => $slot)
        {
            $start = Carbon::parse($slot['start']);

            if($slot['end'] == null)
            {
                $end = $start->copy();

                $end = $end->addHours(2);

            }else {
                $end = Carbon::parse($slot['end']);
            }

            $availability[$key]['start'] = $start;
            $availability[$key]['end'] = $end;
        }

        //create Model
        $user_template = new user_schedule_template;
        // add data to model
        $user_template->shift_max = $validatedData['max'];
        $user_template->preferred = $validatedData['preferred'];
        $user_template->weekly_max = $validatedData['hours'];
        $user_template->schedule_period = $validatedData['period'];
        $user_template->user_id = Auth::id();
        // save model
        $user_template->save();

        $template_id = $user_template->id;

        foreach ($availability as $key => $slot) {
            // create model
            $availability_model = new user_availablity;
            // add data to model  'user_schedule_template'
            $availability_model->available = $slot['available'];
            $availability_model->starts = $slot['start'];
            $availability_model->ends = $slot['end'];
            $availability_model->user_schedule_template = $template_id;
            // save Model
            $availability_model->save();
        }

        return redirect('/');

    }
}
