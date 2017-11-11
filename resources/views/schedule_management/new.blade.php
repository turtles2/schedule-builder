@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Schedule Settings</div>


                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/schedule_management/new" id="form">
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

                   <div class="form-group{{ $errors->has('min') ? ' has-error' : '' }}">
                      <label for="min" class="col-md-4 control-label">Minimum Shift Length</label>

                      <div class="col-md-6">
                          <input id="min" type="number" min="0" class="form-control" name="min" value="{{ old('min') }}" required>

                          @if ($errors->has('min'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('min') }}</strong>
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

                 <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
                    <label for="start" class="col-md-4 control-label">Schedule Start Data</label>

                    <div class="col-md-6">
                        <input id="start" type="text" placeholder="MM/DD/YYY"class="form-control" name="start" value="{{ old('start') }}" required>

                        @if ($errors->has('start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('start') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                   <label for="end" class="col-md-4 control-label">Schedule End Data</label>

                   <div class="col-md-6">
                       <input id="end" type="text" placeholder="MM/DD/YYY"class="form-control" name="end" value="{{ old('start') }}" required>

                       @if ($errors->has('end'))
                           <span class="help-block">
                               <strong>{{ $errors->first('end') }}</strong>
                           </span>
                       @endif
                   </div>
               </div>

                   <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" onclick="cal_loader()" class="btn btn-primary">
                                    Load Settings into Calendar
                                </button>
                            </div>
                        </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Schedule Creator</div>

                <div class="panel-body">

                    <div class="control-group">
                       <label for="workers" class="col-md-4 control-label">Employees Needed for Shift</label>

                       <div class="col-md-6">
                           <input id="workers" type="number" min="1" class="form-control" name="workers">

                       </div>
                   </div>
                    <br />
                    <br />

                       <div class="control-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="button"  onclick="create_shift()"  class="btn btn-primary">
                                        Create Draggable Shift
                                    </button>
                                </div>
                            </div>

                            <br />
                            <br />
                            <br />

                            <div class="panel panel-success">
                                <div class="panel-heading" id="trash">
                                    Shifts
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
                                     Submit Schedule Template
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
        $(document).ready(function(){
          var date_input=$('input[name="start"]'); //our date input has the name "date"
          var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
          var options={
            format: 'mm/dd/yyyy',
            container: container,
            todayHighlight: true,
            autoclose: true,
          };
          date_input.datepicker(options);
        })
    </script>

    <script>
        $(document).ready(function(){
          var date_input=$('input[name="end"]'); //our date input has the name "date"
          var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
          var options={
            format: 'mm/dd/yyyy',
            container: container,
            todayHighlight: true,
            autoclose: true,
          };
          date_input.datepicker(options);
        })
    </script>
    <script>
    function cal_loader() {

        var start = document.getElementById('start').value;
        var end = document.getElementById('end').value;

        $('#calendar-{{$calendar->getId()}}').fullCalendar('option', {
            validRange: {
                start: document.getElementById('start').value,
                end: document.getElementById('end').value,
            },
        });

    }

    var shifts = [];

    function create_shift() {

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

        if(document.getElementById('workers').checkValidity())
        {

            if( jQuery.inArray(document.getElementById('workers').value,shifts) == -1)
            {

            var span = document.createElement('div');

            span.innerHTML = '<a  class="btn btn-success shift" id="test" style="margin-bottom:5px;">\
            ' + document.getElementById('workers').value + ' Employees Needed </a> ';

              document.getElementById('drop-area').appendChild(span);

              shifts.push(document.getElementById('workers').value);

              // make the event draggable using jQuery UI
              $(span).draggable({
                  zIndex: 999,
                  revert: true,      // will cause the event to go back to its
                  revertDuration: 0  //  original position after the drag
              });

              $(span).data('event', {
                   title: document.getElementById('workers').value + ' Employees Needed ',
                   allDay: false,
                   overlap: false,
                   color: 'green',
                   stick: true,
               });


          }

        }

    }

    function build_shifts()
    {
        var eventsFromCalendar = $('#calendar-{{$calendar->getId()}}').fullCalendar('clientEvents');

        var text = JSON.stringify(eventsFromCalendar);

        // this is not running why?

        $('#form').append('<input type="hidden" name="shifts" value="' + text + '" />');

    }

    </script>
@endsection
