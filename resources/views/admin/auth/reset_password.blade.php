@extends('admin.auth.layout')
  
@section('content')
<div class="authincation h-100">
    <div class="container-fluid h-100">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-md-6">
                <div class="authincation-content">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form">
                                <h4 class="text-center mb-4">Reset Password</h4>
                                <form id="ResetForm" method="post">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label><strong>Password</strong></label>
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="********" value="{{ old('password') }}" autofocus>
                                        <div id="password-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Confirm Password</strong></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="********" value="{{ old('confirm_password') }}">
                                        <div id="confirm_password-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary btn-block" id="resetSubmit">
                                            Reset Password <i class="fa fa-spinner fa-spin loadericonfa" style="display:none;"></i>
                                        </button>
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