@extends('adminlte::page')

@section('css')
  <meta id="token" name="token" value="{{ csrf_token() }}" />
  <link href="{{ asset('/public/vendor/bishopm/fullcalendar/fullcalendar.print.css') }}" rel="stylesheet" type="text/css" media="print"/>
  <link href="{{ asset('/public/vendor/bishopm/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('htmlheader_title')
    Dashboard
@endsection

@section('content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                      Logged in as: 
                      @if (isset(Auth::user()->individual))
                          <b>{{Auth::user()->individual->fullname}}</b>
                      @else
                          <b>{{Auth::user()->name}}</b>
                      @endif
                    </div>
                    <div class="panel-body">
                        <div class="col-md-8">
                        <form id="searchform" action="#" method="get" class="sidebar-form">
                            <div class="input-group">
                              <input type="text" id="q" name="q" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                  <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                  </button>
                                </span>
                            </div>
                          </form>
                          <div id="searchdata">
                          </div>
                        </div>
                        <div class="well col-md-4">
                        <h2 style="margin-top: -7px">To do 
                        @if (isset(Auth::user()->individual))
                          <small>{{Auth::user()->individual->firstname}}</small>
                        @endif
                        </h2>
                        <ul class="list-unstyled">
                        @foreach ($actions as $action)
                            <li><a role="button" id="{{$action->id}}" title="Click to mark task as complete" class="toggletask"><i class="fa-square-o fa"></i></a> {{$action->description}}</li>
                        @endforeach
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script type="text/javascript">
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="token"]').attr('value')
      }
  });
  $(document).ready(function() {
      $('.toggletask').on('click',function(){
        $.ajax({
          type : 'GET',
          url : '{{url('/')}}/admin/actions/togglecompleted/' + this.id,
        });
        $(this).find('i').toggleClass('fa-square-o fa-check');
      });
      $(function() {
        $("#q").focus();
      });
      $('#calendar').fullCalendar({
          googleCalendarApiKey: 'AIzaSyD4y1RyWYcv2nqBlp0wJZr6ULGWGt8VrX4',
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          views: {
              week: { columnFormat: 'ddd D/M' },
              day: { columnFormat: 'ddd' },
              month: { columnFormat: 'ddd' }
          },
          events: {!! json_encode($pcals) !!},
          eventSources:  {!! json_encode($cals) !!},
          defaultView: 'agendaWeek'
      });
      $('#q').on('change',function(){
        $.ajax({
            type : 'POST',
            url : '{{route('admin.search')}}',
            data : $('#searchform').serialize(),
            success: function(e){
              $('#searchdata').text(e);
            }
        });
      });
  });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.5/moment.js"></script>
  <script src="{{ asset('/public/vendor/bishopm/fullcalendar/fullcalendar.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('/public/vendor/bishopm/fullcalendar/gcal.js') }}" type="text/javascript"></script>
@endsection