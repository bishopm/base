{{ Form::bsText('title','Title','Title',$resource->title) }}
{{ Form::bsText('description','Description','Description',$resource->description) }}
@if (!count($media))
{{ Form::bsFile('image') }}
@else
<div id="thumbdiv">
	{{ Form::bsImgpreview($media->getUrl(),300,'Image') }}
</div>
<div id="filediv" style="display:none;">
	{{ Form::bsFile('image') }}
</div>
@endif