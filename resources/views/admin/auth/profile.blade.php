@extends('admin.layout.app')
@section('title', 'Profile')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Profile</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        
    </div>
@endsection
@section('content')
               
    <div class="row">
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="profile-tab">
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a href="#update-profile" data-toggle="tab" class="nav-link active show">Update Profile </a>
                                </li>
                                <li class="nav-item"><a href="#change-password" data-toggle="tab" class="nav-link ">Change Password</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                               
                                <div id="update-profile" class="tab-pane fade active show">
                                    <div class="pt-3">
                                        <div class="settings-form">
                                            <form class="form-valide" id="profileupdateform" method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label class="text-label">Full Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Full Name" value="{{ isset($profile->full_name)?$profile->full_name:'' }}">
                                                        <div id="full_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="text-label">Email Address <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" value="{{ isset($profile->email)?$profile->email:'' }}">
                                                                <div id="email-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                            </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="text-label">Mobile Number <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number" name="mobile_no" id="mobile_no" class="form-control" placeholder="Mobile Number" value="{{ isset($profile->mobile_no)?$profile->mobile_no:'' }}">
                                                            <div id="mobile_no-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="text-label">Blood Group <span class="text-danger">*</span></label>
                                                        <select class="single-select-placeholder js-states" name="blood_group">
                                                            <option value="A+" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'A+' ? 'selected' : '' }}>A+</option>
                                                            <option value="A-" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'A-' ? 'selected' : '' }}>A-</option>
                                                            <option value="B+" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'B+' ? 'selected' : '' }}>B+</option>
                                                            <option value="B-" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'B-' ? 'selected' : '' }}>B-</option>
                                                            <option value="O+" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'O+' ? 'selected' : '' }}>O+</option>
                                                            <option value="O-" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'O-' ? 'selected' : '' }}>O-</option>
                                                            <option value="AB+" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'AB+' ? 'selected' : '' }}>AB+</option>
                                                            <option value="AB-" {{ old('blood_group', isset($profile->blood_group) ? $profile->blood_group : '') === 'AB-' ? 'selected' : '' }}>AB-</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="text-label">Gender <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="gender" value="2" {{ old('gender', isset($profile->gender) ? $profile->gender : '') == '2' ? 'checked' : '' }}> Male
                                                            </label>
                                                            &nbsp;&nbsp;&nbsp;
                                                            <label class="radio-inline">
                                                                <input type="radio" name="gender" value="1" {{ old('gender', isset($profile->gender) ? $profile->gender : '') == '1' ? 'checked' : '' }}> Female
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label class="col-form-label" for="profilePic">Profile Image
                                                        </label>
                                                        <input type="file" class="form-control-file" id="profile_pic" onchange=""
                                                            name="profile_pic">
                                                        <div id="profilepic-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                        <img src="{{ isset($profile->profile_pic_url) ? asset($profile->profile_pic_url) : asset('image/avtar.png') }}" class="" id="profilepic_image_show" height="100px" width="100px"
                                                            style="margin-top: 10px;">
                                                    </div>
                                                </div>
                                                <button type="button" id="saveProfileBtn" class="btn btn-primary">Save  <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="change-password" class="tab-pane fade">
                                    <div class="pt-3">
                                        <div class="settings-form">
                                            <form class="form-valide" id="changepassowrdform" method="post">
                                                {{ csrf_field() }}
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <label class="text-label">Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                                            <div id="password-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                        </div>
                                                     
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <label class="text-label">Confirm Password<span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                                                            <div id="password-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" id="savePasswordBtn" class="btn btn-primary">Save  <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
@endsection

@section('js')

    <!-- Datatable -->
    <script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/js/plugins-init/datatables.init.js') }}"></script>

    <script  type="text/javascript">
        $(".single-select-placeholder").select2({
        });

        $('body').on('click', '#saveProfileBtn', function() {
           var btn = $(this);
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#profileupdateform")[0]);

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.user.profile.update') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);

                        if (res.errors.full_name) {
                            $('#full_name-error').show().text(res.errors.full_name);
                        } else {
                            $('#full_name-error').hide();
                        }
                        if (res.errors.email) {
                            $('#email-error').show().text(res.errors.email);
                        } else {
                            $('#email-error').hide();
                        }
                        if (res.errors.mobile_no) {
                            $('#mobile_no-error').show().text(res.errors.mobile_no);
                        } else {
                            $('#mobile_no-error').hide();
                        }
                        if (res.errors.profile_pic) {
                            $('#profile_pic-error').show().text(res.errors.profile_pic);
                        } else {
                            $('#profile_pic-error').hide();
                        }
                    }
                    if (res.status == 200) {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.success("Profile updated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 400) {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });


        $('body').on('click', '#savePasswordBtn', function() {
           var btn = $(this);
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            $('#password-error').html("");
            var formData = new FormData($("#changepassowrdform")[0]);
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.user.password.change') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);

                        if (res.errors.password) {
                            $('#password-error').show().text(res.errors.password);
                        } else {
                            $('#password-error').hide();
                        }
                        if (res.errors.confirm_password) {
                            $('#confirm_password-error').show().text(res.errors.confirm_password);
                        } else {
                            $('#confirm_password-error').hide();
                        }
                    }
                    if (res.status == 200) {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        $("#changepassowrdform").trigger('reset');
                        toastr.success("Password changed successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 400) {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });

        $('#profile_pic').change(function(){
            $('#profile_pic-error').hide();
            var file = this.files[0];
            var fileType = file["type"];
            var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
            if ($.inArray(fileType, validImageTypes) < 0) {
                $('#profile_pic-error').show().text("Please provide a Valid Extension Image(e.g: .jpg .png)");
                var default_image = "{{ asset('images/default_avatar.jpg') }}";
                $('#profilepic_image_show').attr('src', default_image);
            }
            else {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#profilepic_image_show').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
       
    </script>
@endsection