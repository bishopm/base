@extends('adminlte::page')

@section('content')
@include('connexion::shared.errors') 
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6"><h4>Meetings</h4></div>
                            <div class="col-md-6"><a href="{{route('admin.meetings.create')}}" class="btn btn-primary pull-right"><i class="fa fa-pencil"></i> Add a new meeting</a></div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed table-responsive" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Meeting</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Meeting</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($meetings as $meeting)
                                    <tr>
                                        <td></td>{{date("d M Y G:i",$meeting->meetingdatetime)}}</tr><td><a href="{{route('admin.meetings.edit',$meeting->id)}}">{{$meeting->description}}</a></td>
                                    </tr>
                                @empty
                                    <tr><td>No meetings have been added yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script language="javascript">
$(document).ready(function() {
        $('#indexTable').DataTable();
    } );
</script>
@endsection