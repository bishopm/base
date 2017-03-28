<div class="form-group">
  <label for="name">Sermon title</label>
  <input class="form-control" data-slug="source" placeholder="Title" name="title" id="title" type="text" value="{{$sermon->title}}">
</div>
<div class="form-group">
  <label for="slug">Slug</label>
  <input class="form-control" data-slug="target" placeholder="Slug" name="slug" id="slug" type="text" value="{{$sermon->slug}}">
</div>
{{ Form::bsText('servicedate','Service date','Service date',$sermon->servicedate) }}
{{ Form::bsText('mp3','Link to mp3','Link to mp3',$sermon->mp3) }}
{{ Form::bsText('readings','Readings','Readings',$sermon->readings) }}
<div class='form-group '>
  <label for="individual_id">Preacher</label>
  <select class="selectize" id="individual_id" name="individual_id">
    @foreach ($preachers as $preacher)
    	@if ($preacher->id==$sermon->individual_id)
	        <option selected value="{{$preacher->id}}">{{$preacher->firstname}} {{$preacher->surname}}</option>
	    @else
	    	<option value="{{$preacher->id}}">{{$preacher->firstname}} {{$preacher->surname}}</option>
	    @endif
    @endforeach
  </select>
</div> 
{{ Form::bsHidden('series_id',$series) }}