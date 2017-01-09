@extends('adminlte::page')

@section('content_header')
{{ Form::pgHeader('Add service','Services',route('admin.services.index',$society)) }}
@stop

@section('content')
    @include('base::shared.errors')    
    {!! Form::open(['route' => array('admin.services.store',$society), 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('base::services.partials.create-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Create',route('admin.services.index',$society)) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop