@extends('adminlte::page')

@section('content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6"><h4>Households</h4></div>
                            <div class="col-md-6"><a class="btn btn-primary pull-right">Add</a></div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table id="indexTable" class="display" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Household</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Household</th>
                                    <th>Address</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($households as $household)
                                    <tr>
                                        <td><a href="{{route('admin.households.show',$household->id)}}">{{$household->addressee}}</a></td>
                                        <td><a href="{{route('admin.households.show',$household->id)}}">{{$household->addr1}}</a></td>
                                    </tr>
                                @empty
                                    <tr><td>No households have been added yet</td></tr>
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