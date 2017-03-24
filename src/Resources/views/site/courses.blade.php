@extends('connexion::templates.webpage')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
@stop

@section('content')
<img class="img-responsive" src="{{ asset('vendor/bishopm/images/webpageheader.png') }}">
<div class="container">
<h1>{{$setting['site_abbreviation']}} Courses</h1>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#courses" aria-controls="courses" role="tab" data-toggle="tab">Courses</a></li>
    <li role="presentation"><a href="#homegroups" aria-controls="homegroups" role="tab" data-toggle="tab">Home group materials</a></li>
    <li role="presentation"><a href="#selfstudy" aria-controls="selfstudy" role="tab" data-toggle="tab">Self-study</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="courses"><br>
    	@forelse($courses as $course)
    		{{$course}}
    	@empty
    		No courses have been added to the site yet
    	@endforelse
    </div>
    <div role="tabpanel" class="tab-pane" id="homegroups"><br>
        <table id="hgTable" class="table table-striped table-hover table-condensed table-responsive" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th></th><th>Course</th><th>Description</th><th>Average rating (1-5)</th><th>Comments</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th><th>Course</th><th>Description</th><th>Average rating (1-5)</th><th>Comments</th>
                </tr>
            </tfoot>
            <tbody>
                @forelse ($homegroup as $hg)
                    <tr>
                    	<td><a href="{{route('webresource',$hg->slug)}}"><img width="150px" class="img-responsive" src="{{$hg->getMedia('image')->first()->getUrl()}}"></a></td>
                        <td><a href="{{route('webresource',$hg->slug)}}">{{$hg->title}}</a></td>
                        <td><a href="{{route('webresource',$hg->slug)}}">{!!$hg->description!!}</a></td>
                        <td><a href="{{route('webresource',$hg->slug)}}">{{$hg->averageRate()}}</a></td>
                        <td><a href="{{route('webresource',$hg->slug)}}">{{count($hg->comments)}}</a></td>
                    </tr>
                @empty
                    <tr><td>No home group materials have been added yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div role="tabpanel" class="tab-pane" id="selfstudy"><br>
    	@forelse($selfstudy as $ss)
    		{{$ss}}
    	@empty
    		No self-study courses have been added to the site yet
    	@endforelse
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script language="javascript">
$(document).ready(function() {
    $('#hgTable').DataTable();
} );
</script>
@endsection