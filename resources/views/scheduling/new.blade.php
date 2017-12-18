@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Schedule Settings</div>


                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/scheduling/availability" id="form">
                                           {{ csrf_field()}}

                    <div class="form-group{{ $errors->has('max') ? ' has-error' : '' }}">
                       <label for="max" class="col-md-4 control-label">Maximum Shift Length</label>

                       <div class="col-md-6">
                           <input id="max" type="number" min="0" class="form-control" name="max" value="{{ old('max') }}" required>

                           @if ($errors->has('max'))
                               <span class="help-block">
                                   <strong>{{ $errors->first('max') }}</strong>
                               </span>
                           @endif
                       </div>
                   </div>

                  <div class="form-group{{ $errors->has('preferred') ? ' has-error' : '' }}">
                     <label for="preferred" class="col-md-4 control-label">Preferred Shift Length</label>

                     <div class="col-md-6">
                         <input id="preferred" type="number" min="0" class="form-control" name="preferred" value="{{ old('preferred') }}" required>

                         @if ($errors->has('preferred'))
                             <span class="help-block">
                                 <strong>{{ $errors->first('preferred') }}</strong>
                             </span>
                         @endif
                     </div>
                 </div>

                 <div class="form-group{{ $errors->has('hours') ? ' has-error' : '' }}">
                    <label for="hours" class="col-md-4 control-label">Maximum Hours per Week</label>

                    <div class="col-md-6">
                        <input id="hours" type="number" min="1" class="form-control" name="hours" value="{{ old('preferred') }}" required>

                        @if ($errors->has('hours'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hours') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                 <div class="form-group">
                            <label for="period" class="col-md-4 control-label">Schedule Dates</label>

                            <div class="col-md-6">
                                <select class="form-control" id="period" name="period" required>
                                <option value='' selected disabled>Please Select an Option</option>
                                @foreach ($schedules as $key => $schedule)
                                    <option value='{{$schedule->id}}'>Starts: {{$schedule->starts}} Ends: {{$schedule->ends}}</option>
                                @endforeach


                                </select>

                                @if ($errors->has('period'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('period') }}</strong>
                                    </span>
                                @endif
                                                            </div>
                        </div>


                   <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" onclick="cal_loader()" class="btn btn-primary">
                                    Enter Availability
                                </button>
                            </div>
                        </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Schedule Creator</div>

                <div class="panel-body">

                            <div class="panel panel-success">
                                <div class="panel-heading" id="trash">
                                    Availability Options
                                    <a class="btn btn-default btn-lg pull-right" >
                                    <span class="glyphicon glyphicon-trash" aria-hidden="true">Trash</span>

                                </a>

                                <br />
                                <br />
                                </div>
                                <div class="panel-body" id="drop-area">

                                </div>
                                </div>


                            <div class="control-group">
                    {!! $calendar->calendar() !!}
                    {!! $calendar->script() !!}
                </div>
                    <br />
                    <br />
                    <div class="form-group">
                             <div class="col-md-6 col-md-offset-0">
                                 <button  class="btn btn-primary" type="submit" onclick="build_shifts()">
                                     Submit Availability
                                 </button>
                             </div>

                         </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer_scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>

    <script>
    var id = null;

    var ran = false;
    var periods = [
        @foreach ($schedules as $key => $schedule)
                {
                id:{{$schedule->id}},
                starts:'{{$schedule->starts}}',
                ends:'{{$schedule->ends}}'
            },
        @endforeach
    ]

    function cal_loader() {

        $('#calendar-{{$calendar->getId()}}').fullCalendar("removeEventSource",
        {
            url: '/scheduling/availability/template',
            type: 'POST',
            data: {
                id: window.id,
            },
            color: 'orange',   // a non-ajax option
            textColor: 'black', // a non-ajax option
            editable: false
        }
        );

        $('#calendar-{{$calendar->getId()}}').fullCalendar('removeEvents');

        window.id = document.getElementById('period').value;

        $('#calendar-{{$calendar->getId()}}').fullCalendar("addEventSource",
                {
                url: '/scheduling/availability/template',
                type: 'POST',
                data: {
                    id: window.id,
                },
                color: 'orange',   // a non-ajax option
                textColor: 'black', // a non-ajax option
                editable: false
            }
        );

        $('#calendar-{{$calendar->getId()}}').fullCalendar('refetchEvents');

        start = null;
        end = null;
        var arrayLength = periods.length;
        for (var i = 0; i < arrayLength; i++) {
            if(periods[i].id == window.id)
            {
                start = periods[i].starts;
                end = periods[i].ends;

                break;
            }

        }

        $('#calendar-{{$calendar->getId()}}').fullCalendar('option', {
            validRange: {
                start: start,
                end: end,
            },
        });

        $('#calendar-{{$calendar->getId()}}').fullCalendar('option', {
            drop: function() {
            },
            eventDragStop: function( event, jsEvent, ui, view ) {

                if(isEventOverDiv(jsEvent.clientX, jsEvent.clientY)) {
                    $('#calendar-{{$calendar->getId()}}').fullCalendar('removeEvents', event._id);

                }
            }
    });


        var isEventOverDiv = function(x, y) {

            var external_events = $( '#trash' );
            var offset = external_events.offset();
            offset.right = external_events.width() + offset.left;
            offset.bottom = external_events.height() + offset.top - $(document).scrollTop();

            // Compare
            if (x >= offset.left
                && y >= (offset.top - $(document).scrollTop())
                && x <= offset.right
                && y <= offset .bottom) { return true; }
            return false;

        }
            if(ran != true)
            {
                var span = document.createElement('div');

                span.innerHTML = '<a  class="btn btn-success shift" id="test" style="margin-bottom:5px;">\
                ' + ' Available to Work </a> ';

                  document.getElementById('drop-area').appendChild(span);

                  // make the event draggable using jQuery UI
                  $(span).draggable({
                      zIndex: 999,
                      revert: true,      // will cause the event to go back to its
                      revertDuration: 0  //  original position after the drag
                  });

                  $(span).data('event', {
                       title: 'Available to Work ',
                       allDay: false,
                       overlap: false,
                       color: 'green',
                       stick: true,
                   });

                   var span2 = document.createElement('div');

                   span2.innerHTML = '<a  class="btn btn-danger shift" id="test" style="margin-bottom:5px;">\
                   ' + 'Not Available to Work </a> ';

                     document.getElementById('drop-area').appendChild(span2);

                     // make the event draggable using jQuery UI
                     $(span2).draggable({
                         zIndex: 999,
                         revert: true,      // will cause the event to go back to its
                         revertDuration: 0  //  original position after the drag
                     });

                     $(span2).data('event', {
                          title: 'Not Available to Work ',
                          allDay: false,
                          overlap: false,
                          color: 'red',
                          stick: true,
                      });

                     ran = true;
              }
    }

    function build_shifts()
    {

        var events = $('#calendar-{{$calendar->getId()}}').fullCalendar("clientEvents");

        var data = [];

        events.forEach(loader);

            function loader(value)
            {

                var event = [value.title,value.start,value.end];

                data.push(event);
            }

        $('#form').append('<input type="hidden" name="shifts" value="' + data + '" />');

    }

    </script>
@endsection
