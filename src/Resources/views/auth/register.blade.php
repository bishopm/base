@extends('adminlte::master')

@section('adminlte_css')
    <meta id="token" name="token" value="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('public/vendor/adminlte/plugins/iCheck/square/blue.css') }}">
    <link rel="stylesheet" href="{{ asset('public/vendor/adminlte/css/auth.css') }}">
    <link href="{{ asset('/public/vendor/bishopm/css/selectize.css') }}" rel="stylesheet" type="text/css" />
    @yield('css')
@stop

@section('body_class', 'register-page')

@section('body')
<div class="register-box">
    <div class="register-logo">
        <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}"><b>Umhlali</b> Methodist Church</a>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (isset($errmsg))
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul><li>{{ $errmsg }}</li></ul>
        </div>
    @endif    

    <div class="register-box-body">
        <p class="login-box-msg"><b>Register as a new user</b></p>
        <p class="login-box-msg">Choose a unique username (eg: johnsmith) and enter the email address that you have think we have on record for you. If that email address is in our existing database, you'll be able to select your name and we'll send you a mail to make sure you are you who say you are :)<br><br>If your name or email address are not on our system, click <a href="register-user">here</a> and we'll help you sign up.</p>
        <form action="{{ url('/register') }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group has-feedback">
                <input class="form-control" placeholder="Username" id="name" name="name" autocomplete="off" value="{{ old('name') }}"/>
                <div id="errmess" style="display:none;"><i class="fa fa-times"></i> Username is required and must be unique</div>
                <div id="okmess" style="display:none;"><i class="fa fa-check"></i> This username is available</div>
            </div>
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Email" id="email" name="email"/>
                <span class="fa fa-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <select class="selectize" placeholder="Find your name in our database" name="individual_id" id="individual_id" value="{{ old('individual_id') }}"/>
                </select>
            </div>
            <div class="form-group">
                <label>Which service do you usually attend?</label>
                <select name="service_id" class="form-control" id="service_id">
                    @foreach ($society->services as $service)
                        <option value="{{$service->id}}">{{$service->servicetime}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" name="password"/>
                <span class="fa fa-key form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Retype password" name="password_confirmation"/>
                <span class="fa fa-sign-in form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                  <a href="{{ url('/login') }}" class="text-center">I have already registered</a>
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                </div><!-- /.col -->
            </div>
        </form>
    </div><!-- /.form-box -->
</div><!-- /.register-box -->


@endsection

@section('adminlte_js')
    <script src="{{ asset('public/vendor/adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    <script src="{{ asset('public/vendor/bishopm/js/selectize.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            $.ajaxSetup({
              headers: {
                 'X-CSRF-TOKEN': $('meta[name="token"]').attr('value')
              }
            });
            $('.selectize').selectize({
                openOnFocus: 1
            });
            $('#email').on('blur',function(e){
                email=e.target.value;
                $(".selectize")[0].selectize.clearOptions();
                if (email){
                    $.post('checkmail', { "email": email}, 
                    function(data){
                        var selectize = $(".selectize")[0].selectize;
                        if (data!=="No data"){
                            var indivs = $.parseJSON(data);
                            for (var i = 0; i < indivs.length; i++) {
                                selectize.addOption({
                                    text:indivs[i].surname + ', ' + indivs[i].firstname,
                                    value: indivs[i].id
                                });
                                selectize.open();
                            }
                        } else {
                            selectize.addOption({
                                    text:'Matching record not found in database',
                                    value: 0
                            });
                            selectize.open();
                        }
                    }); 
                }
            });                   
            $('#name').bind('input', function(){
            if ($('#name').val()!==''){
                usercheck($('#name').val());
            } else {
                $('#errmess').show();
                $('#okmess').hide();
            }
        });
        });
        function usercheck(username){
            $.ajax({
                type : 'GET',
                url : '{{url('/')}}' + '/admin/newuser/checkname/' + username,
                success: function(e){
                    if (e=='error'){
                        $('#errmess').show();
                        $('#okmess').hide();
                    } else {
                        $('#errmess').hide();
                        $('#okmess').show();
                    }
                }
            });
        };
    </script>
    @yield('js')
@stop 
