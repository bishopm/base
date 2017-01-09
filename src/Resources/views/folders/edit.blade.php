@extends('adminlte::page')

@section('content')
    {{ Form::pgHeader('Edit folder','Folders',route('admin.folders.index')) }}
    @include('base::shared.errors')    
    {!! Form::open(['route' => array('admin.folders.update',$folder->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('base::folders.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.folders.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop