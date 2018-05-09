@extends('layouts.login')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <p class="text-login"
                            style="
                            text-align: center;
                            font-size: 20px;
                            margin-top: 3rem;
                            ">
                        LOGIN TO USE WEBSITE</p>

                        <div class="form-group">
                            <div class="col-xs-3"></div>
                            <div class="col-xs-6">
                                <a class="btn btn-danger"
                                    style="
                                    width: 100%;
                                    margin-top: 5rem;
                                    box-shadow: 1px 4px 4px rgba(0, 0, 0, .5);
                                        "
                                    href="{{ url('auth/google') }}"
                                >
                                    <i class="fa fa-google-plus" aria-hidden="true"></i>
                                    Login with Google
                                </a>
                            </div>
                            <div class="col-xs-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .panel {
        height: 30vh;
        box-shadow: 1px 4px 4px rgba(0, 0, 0, .5);
        background: #f2f2f2;
    }
</style>
@endsection
