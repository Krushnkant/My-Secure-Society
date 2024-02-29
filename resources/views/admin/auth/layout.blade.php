<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $page }}</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
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
    $('#LoginForm').on('submit', function (e) {
        $("#email-error").html("");
        $("#password-error").html("");
        var thi = $(this);
        $('#loginSubmit').find('.loadericonfa').show();
        $('#loginSubmit').prop('disabled',true);
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.postlogin') }}",
            data: formData,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#loginSubmit').find('.loadericonfa').hide();
                    $('#loginSubmit').prop('disabled',false);
                    if (res.errors.Email) {
                        $('#email-error').show().text(res.errors.Email);
                    } else {
                        $('#email-error').hide();
                    }

                    if (res.errors.Password) {
                        $('#password-error').show().text(res.errors.Password);
                    } else {
                        $('#password-error').hide();
                    }
                }

                if(res.status == 200){
                    $('#loginSubmit').prop('disabled',false);
                    toastr.success("You have Successfully loggedin",'Success',{timeOut: 5000});
                    location.href ="{{ url('admin/dashboard') }}";
                }

                if(res.status == 300){
                    $('#loginSubmit').find('.loadericonfa').hide();
                    $('#loginSubmit').prop('disabled',false);
                    toastr.error("Your Account is Deactive..Please Contact Admin",'Error',{timeOut: 5000});
                }

                if(res.status == 400){
                    $('#loginSubmit').find('.loadericonfa').hide();
                    $('#loginSubmit').prop('disabled',false);
                    toastr.error("Opps! You have entered invalid credentials",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $('#loginSubmit').find('.loadericonfa').hide();
                $('#loginSubmit').prop('disabled',false);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

</script>
<!--login page JS end -->

</body>

</html>