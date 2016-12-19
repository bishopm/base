@extends('adminlte::page')

@section('content_header')
    {{ Form::pgHeader('Edit a group','Groups',route('admin.groups.index')) }}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.groups.update',$group->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('base::groups.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.groups.index')) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="map_canvas" style="height:350px;">
            </div>
            {{ Form::bsText('latitude','Latitude','Latitude',$group->latitude) }}
            {{ Form::bsText('longitude','Longitude','Longitude',$group->longitude) }}
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('js')
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{$setting['google_api']}}"></script>
    <script src="{{url('/')}}/vendor/bishopm/js/gmap.js" type="text/javascript"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            google.maps.event.addDomListener(window, 'load', initialize(12));
        });
    </script>
@stop