<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $page }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
</head>

<body class="h-100">
    @yield('content')


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('/js/custom.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('/vendor/toastr/js/toastr.min.js') }}"></script>

    <!--login page JS start -->
    <script type="text/javascript">
        $('#LoginForm').on('submit', function(e) {
            $("#email-error").html("");
            $("#password-error").html("");
            var thi = $(this);
            $('#loginSubmit').find('.loadericonfa').show();
            $('#loginSubmit').prop('disabled', true);
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.postlogin') }}",
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $('#loginSubmit').find('.loadericonfa').hide();
                        $('#loginSubmit').prop('disabled', false);
                        if (res.errors.email) {
                            $('#email-error').show().text(res.errors.email);
                        } else {
                            $('#email-error').hide();
                        }

                        if (res.errors.password) {
                            $('#password-error').show().text(res.errors.password);
                        } else {
                            $('#password-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        $('#loginSubmit').prop('disabled', false);
                        toastr.success("You have successfully login", 'Success', {
                            timeOut: 5000
                        });
                        location.href = "{{ url('admin/dashboard') }}";
                    }

                    if (res.status == 300) {
                        $('#loginSubmit').find('.loadericonfa').hide();
                        $('#loginSubmit').prop('disabled', false);
                        toastr.error("Your Account is Deactive..Please Contact Admin", 'Error', {
                            timeOut: 5000
                        });
                    }

                    if (res.status == 400) {
                        $('#loginSubmit').find('.loadericonfa').hide();
                        $('#loginSubmit').prop('disabled', false);
                        toastr.error("Opps! You have entered invalid credentials", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $('#loginSubmit').find('.loadericonfa').hide();
                    $('#loginSubmit').prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });

        $('#ForgetForm').on('submit', function(e) {
            $("#email-error").html("");
            $("#password-error").html("");
            var thi = $(this);
            $('#forgetSubmit').find('.loadericonfa').show();
            $('#forgetSubmit').prop('disabled', true);
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.postforgetpassword') }}",
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $('#forgetSubmit').find('.loadericonfa').hide();
                        $('#forgetSubmit').prop('disabled', false);
                        if (res.errors.email) {
                            $('#email-error').show().text(res.errors.email);
                        } else {
                            $('#email-error').hide();
                        }
                    }
                    if (res.status == 200) {
                        $('#forgetSubmit').find('.loadericonfa').hide();
                        $('#forgetSubmit').prop('disabled', false);
                        $("#ForgetForm")[0].reset();
                        $("#successMailModal").modal('show');
                    }

                    if (res.status == 400) {
                        $('#forgetSubmit').find('.loadericonfa').hide();
                        $('#forgetSubmit').prop('disabled', false);
                        toastr.error("Opps! You have entered invalid credentials", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $('#forgetSubmit').find('.loadericonfa').hide();
                    $('#forgetSubmit').prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });

        $('#ResetForm').on('submit', function(e) {
            $("#email-error").html("");
            $("#password-error").html("");
            var thi = $(this);
            $('#resetSubmit').find('.loadericonfa').show();
            $('#resetSubmit').prop('disabled', true);
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.postResetPassword') }}",
                data: formData,
                success: function(res) {
                    console.log(res);
                    if (res.status == 'failed') {
                        $('#resetSubmit').find('.loadericonfa').hide();
                        $('#resetSubmit').prop('disabled', false);

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
                        $('#resetSubmit').prop('disabled', false);
                        toastr.success("You have Successfully Reset Password", 'Success', {
                            timeOut: 5000
                        });
                        location.href = "{{ route('admin.login') }}";
                    }

                    if (res.status == 400) {
                        $('#resetSubmit').find('.loadericonfa').hide();
                        $('#resetSubmit').prop('disabled', false);
                        toastr.error("Opps! You have entered invalid credentials", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $('#resetSubmit').find('.loadericonfa').hide();
                    $('#resetSubmit').prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });
    </script>
    <!--login page JS end -->

</body>

</html>
