@extends('adminlte::page')

@section('content_header')
    {{ Form::pgHeader('Edit menuitem','Menuitems',route('admin.menuitems.index',$menu)) }}
@stop

@section('content')
    {!! Form::open(['route' => array('admin.menuitems.update',$menu,$menuitem->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-6">
            <a href="{{route('admin.menuitems.create',$menu)}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add a new menuitem item</a>
        </div>
        <div class="col-md-6">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('base::menuitems.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.menuitems.index',$menu)) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop