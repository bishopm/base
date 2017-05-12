@extends('connexion::templates.backend')

@section('content')
<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">{{$roster->rostername}} ({{date("d F Y",strtotime($rosterdate))}}) 
    <small>Available credits: {{$credits}}</small></h3>
    <span class="pull-right">
      {!! Form::open(['method'=>'post','url'=>'/admin/rosters/' . $roster->id . '/sms/send'])!!}
      @if (isset($extrainfo))
          @foreach ($extrainfo as $key=>$extra)
          	<input type="hidden" name="extrainfo[{{$key}}]" value="{{$extra}}">
          @endforeach
      @endif
      {!! Form::submit('Send SMS messages',array('class'=>'btn btn-primary btn-md'))!!}
      {!! Form::close() !!}
    </span>
  </div>
  <div class="box-body">
    @if (isset($rosterdetails))
      <ul class="list-unstyled">
        @foreach ($rosterdetails as $rosterdetail)
  	        <li><i>{{$rosterdetail['message']}}</i> | [To be sent to {{$rosterdetail['recipient']}} on {{$rosterdetail['cellphone']}}]</l1>
        @endforeach
      </ul>
    @endif
  </div>
</div>
@stop
