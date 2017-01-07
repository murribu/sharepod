@extends('spark::layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    @include('spark::shared.errors')

                    <div class="form-group">
                        <div class="row" style="margin-bottom:15px;margin-top:30px;">
                            <div class="col-xs-6 col-xs-offset-3">
                                <a title="Twitter" class="btn btn-block btn-social btn-twitter" href="/auth/twitter" onclick="window.open('/auth/twitter','auth','width=500,height=450');return false;">
                                    <span class="fa fa-twitter"></span> Sign in with Twitter
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-xs-offset-3">
                                <a title="Facebook" class="btn btn-block btn-social btn-facebook" href="/auth/facebook" onclick="window.open('/auth/facebook','auth','width=500,height=450');return false;">
                                    <i class="fa fa-facebook"></i> Sign in with Facebook
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="position:relative;padding:50px;">
                        <div style="border-top: #333 solid;width: 100%;top: 0px;z-index: 1;left: 50%;" ></div>
                        <h4 style="z-index: 2;margin: 2px;background-color: #fff;left: calc(50% - 11px);position: absolute;top: calc(50% - 15px);padding:5px;">OR</h4>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" action="/login">
                        {{ csrf_field() }}

                        <!-- E-Mail Address -->
                        <div class="form-group">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa m-r-xs fa-sign-in"></i>Login
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
