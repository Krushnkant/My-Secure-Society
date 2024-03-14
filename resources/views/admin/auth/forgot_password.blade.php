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
                                    <h4 class="text-center mb-4">Forgot Password?</h4>
                                    <form method="post" id="ForgetForm">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" name="email" id="email" class="form-control" value="" placeholder="Enter Your Email">
                                            <div id="email-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-primary btn-block" type="submit" id="forgetSubmit">Send Password Reset Link <i class="fa fa-spinner fa-spin loadericonfa" style="display:none;"></i></button>
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

    <div class="modal fade" id="successMailModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header justify-content-end">
              <span type="button" class="close-modal-icon" data-dismiss="modal">
                  <i class="tio-clear"></i>
              </span>
            </div>
            <div class="modal-body">
              <div class="forget-pass-content">
                  <!-- After Succeed -->
                  <img src="{{asset('image/sent-mail.svg')}}" alt="">
                  <h4>
                    A mail has been sent to your registered email!
                  </h4>
                  <p>
                    Click the link in the mail description to change password
                  </p>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
  