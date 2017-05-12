@extends('connexion::templates.backend')

@section('css')
    @parent
@stop

@section('content')
@include('connexion::shared.errors') 

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-6"><h4>Courses</h4></div>
                        <div class="col-md-6"><a href="{{route('admin.courses.create')}}" class="btn btn-primary pull-right"><i class="fa fa-pencil"></i> Add a new course</a></div>
                        <hr>
                    </div>
                </div>
                <div class="panel-body">
                    <table id="indexTable" class="table table-striped table-hover table-condensed table-responsive" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Course</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Course</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @forelse ($courses as $course)
                                <tr>
                                    <td><a href="{{route('admin.courses.edit',$course->id)}}">{{$course->title}}</a></td>
                                </tr>
                            @empty
                                <tr><td>No courses have been added yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@parent
<script language="javascript">
$(document).ready(function() {
        $('#indexTable').DataTable();
    } );
</script>
@endsection