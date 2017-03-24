@extends('connexion::templates.webpage')

@section('css')
<link href="{{ asset('/vendor/bishopm/mediaelement/build/mediaelementplayer.css') }}" rel="stylesheet" type="text/css" />
<meta id="token" name="token" value="{{ csrf_token() }}" />
@endsection

@section('content')
<img class="img-responsive" src="{{ asset('vendor/bishopm/images/webpageheader.png') }}">
<div class="container">
	<div class="row">
		<h3 class="text-center">{{$sermon->sermon}}</h3>
		<div class="col-md-4 text-center">
			<img class="top17" src="{{$sermon->series->getMedia('image')->first()->getUrl()}}">
    	    <audio class="center-block" controls="" width="250px" preload="none" height="30px" src="{{$sermon->mp3}}"></audio>
            <div class="col-md-12">{{date("j M", strtotime($sermon->servicedate))}}: {{$sermon->sermon}}</div>
        	<div class="col-md-12"><a href="{{url('/')}}/people/{{$sermon->individual->slug}}">{{$sermon->individual->firstname}} {{$sermon->individual->surname}}</a></div>
    	</div>
    	<div class="col-md-8">
		    @include('connexion::shared.comments', ['entity' => $sermon])
    	</div>
	</div>
</div>
@endsection

@section('js')
<script src="{{ asset('vendor/bishopm/mediaelement/build/mediaelement.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendor/bishopm/mediaelement/build/mediaelementplayer.js') }}" type="text/javascript"></script>
<script type="text/javascript">
(function ($) {
  jQuery(window).on('load', function() {
    $('audio').mediaelementplayer({
      features: ['playpause','tracks','progress','volume'],
    });
  });
})(jQuery);
</script>
@include('connexion::shared.commentsjs', ['url' => route('admin.blogs.addcomment',$sermon->id)])
@endsection