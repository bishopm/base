@extends('connexion::templates.backend')

@section('css')
    @parent
@stop

@section('content')
    <div class="container-fluid spark-screen">
        @include('connexion::shared.errors') 
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Worship Service Statistics
                                @foreach ($servicetimes as $service)
                                    <a href="{{url('/')}}/admin/statistics/historygraph/{{$service}}" style="margin-left:5px;" class="btn btn-xs btn-primary">History: {{$service}}</a>
                                @endforeach
                                </small></h4>
                            </div>
                            <div class="col-md-6">
                                <a href="{{url('/')}}/admin/statistics/create" style="margin-left:5px;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add figure</a>
                                <a href="{{url('/')}}/admin/statistics/graph" class="btn btn-primary pull-right"><i class="fa fa-line-chart"></i> Current graph</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed table-responsive" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Service date</th>
                                    @foreach ($servicetimes as $servicetime)
                                        <th>{{$servicetime}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Service date</th>
                                    @foreach ($servicetimes as $servicetime)
                                        <th>{{$servicetime}}</th>
                                    @endforeach
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($statistics as $statdate=>$stat)
                                    <tr>
                                        <td>{{$statdate}}</td>
                                        @foreach ($servicetimes as $servicetime)
                                            @if (isset($stat[$servicetime]['attendance']))
                                                <td><a href="{{route('admin.statistics.edit',$stat[$servicetime]['id'])}}">{{$stat[$servicetime]['attendance']}}</a></td>
                                            @else
                                                <td>-</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td>No statistics have been added yet</td></tr>
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
@parent
<script language="javascript">
$(document).ready(function() {
        $('#indexTable').DataTable( {
            "order": [[ 0, "desc" ]]
        } );
    } );
</script>
@endsection