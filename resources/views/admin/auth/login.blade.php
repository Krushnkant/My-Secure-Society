@extends('admin.auth.layout')

@section('content')

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-5">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <h2 class="text-center mb-4">Sign In</h2>
                                    <form method="post" action="" id="LoginForm">
                                    {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="email"><strong>Email</strong></label>
                                            <input type="email" name="email" id="email" class="form-control" placeholder="Email Id" value="{{ old('email') }}">
                                            <div id="email-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password"><strong>Password</strong></label>
                                            <input type="password" name="password" id="password" class="form-control" placeholder="Password"  value="{{ old('password') }}">
                                            <div id="password-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <!-- <div class="form-check ml-2">
                                                    <input class="form-check-input" type="checkbox" id="basic_checkbox_1">
                                                    <label class="form-check-label" for="basic_checkbox_1">Remember me</label>
                                                </div> -->
                                            </div>
                                            <div class="form-group">
                                                <a href="{{ URL::to('forgot-password') }}">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="text-center mt-5">
                                            <button type="submit" id="loginSubmit" class="btn btn-primary btn-block">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
  