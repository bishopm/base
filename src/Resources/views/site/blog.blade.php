@extends('connexion::templates.webpage')

@section('css')
  <meta id="token" name="token" value="{{ csrf_token() }}" />
@stop

@section('content')
<div class="container">
	<div class="row">
	  	<div class="col-md-9">
			<h3>{{$blog->title}} <small><a href="{{url('/')}}/people/{{$blog->individual->slug}}">{{$blog->individual->firstname}} {{$blog->individual->surname}}</a>&nbsp;
		  	@foreach ($blog->tags as $tag)
		  		<a class="label label-primary" href="{{url('/')}}/subject/{{$tag->name}}">{{$tag->name}}</a></b>&nbsp;
		  	@endforeach
		  	</small></h3>
		  	@if (count($media))
		  		<img style="float:left; margin-right:15px;" src="{{$blog->getMedia('image')->first()->getUrl()}}">
			@endif
			{!!$blog->body!!}
  	    	@include('connexion::shared.comments')
  	  	</div>
	  	<div class="col-md-3">
	  		<h3>Explore by subject</h3>
	  		{!!$cloud->render()!!}
	  	</div>
	</div>
</div>
@endsection

@section('js')
  @include('connexion::shared.commentsjs', ['url' => route('admin.blogs.addcomment',$blog->id)])
@endsection