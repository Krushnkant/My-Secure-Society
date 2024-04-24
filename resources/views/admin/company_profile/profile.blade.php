@extends('admin.layout.app')
@section('title', 'Company Profile')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Company Profile</h4>
        </div>
    </div>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12 col-xxl-12">
        <div class="card">
            <div class="card-body">
                <div class="basic-form">
                    <form class="form-valide" id="companyform" method="post">
                        {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group ">
                                        <label class="col-form-label" for="profile_pic">Logo
                                        </label>
                                        <input type="file" class="form-control-file" id="profile_pic" name="profile_pic">
                                        <div id="profile_pic-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group ">
                                        <img src="{{ asset(isset($company->logo_url)?$company->logo_url:'image/placeholder.png') }}" class=""
                                            id="profilepic_image_show" height="100px" width="150px"
                                            style="margin-top: 10px;">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" name="company_name" id="company_name" class="form-control"
                                            placeholder="Company Name" value="{{ isset($company->company_name)?$company->company_name:'' }}">
                                        <div id="company_name-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="gst_in_number">GST Number <span class="text-danger">*</span></label>
                                        <input type="text" name="gst_in_number" id="gst_in_number" class="form-control"
                                            placeholder="GST Number" value="{{ isset($company->gst_in_number)?$company->gst_in_number:'' }}">
                                        <div id="gst_in_number-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="street_address1">Street Address <span class="text-danger">*</span></label>
                                        <textarea name="street_address1" id="street_address1" class="form-control">{{ isset($company->street_address1)?$company->street_address1:'' }}</textarea>
                                        <div id="street_address1-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="street_address2">Street Address 2 </label>
                                        <textarea name="street_address2" id="street_address2" class="form-control">{{ isset($company->street_address2)?$company->street_address2:'' }}</textarea>
                                        <div id="street_address2-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="landmark">Landmark <span class="text-danger">*</span></label>
                                        <input type="text" name="landmark" id="landmark" class="form-control" value="{{ isset($company->landmark)?$company->landmark:'' }}"
                                            placeholder="Landmark">
                                        <div id="landmark-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label" for="pin_code">Pin Code <span class="text-danger">*</span></label>
                                        <input type="text" name="pin_code" id="pin_code" class="form-control" value="{{ isset($company->pin_code)?$company->pin_code:'' }}"
                                            placeholder="Pin Code">
                                        <div id="pin_code-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label">Country <span class="text-danger">*</span></label>
                                        <select class=" js-states" name="country_id" id="country-dropdown">
                                            <option value=""></option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->country_id }}">
                                                    {{ $country->country_name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="country_id-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label">State <span class="text-danger">*</span></label>
                                        <select class=" js-states" name="state_id" id="state-dropdown">
                                            <option value=""></option>
                                        </select>
                                        <div id="state_id-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <div class="form-group">
                                        <label class="text-label">City <span class="text-danger">*</span></label>
                                        <select class=" js-states" name="city_id" id="city-dropdown">
                                            <option value=""></option>
                                        </select>
                                        <div id="city_id-error" class="invalid-feedback animated fadeInDown"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                                
                            </div>
                            <input type="hidden" name="id" id="id" value="{{ isset($company->company_profile_id)?$company->company_profile_id:'' }}">
                            <input type="hidden" name="country" id="country_id" value="{{ isset($company->country_id)?$company->country_id:'' }}">
                            <input type="hidden" name="state" id="state_id" value="{{ isset($company->state_id)?$company->state_id:'' }}">
                            <input type="hidden" name="city" id="city_id" value="{{ isset($company->city_id)?$company->city_id:'' }}">
                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_edit(12)))

                            <div class="mt-5 btn-page">
                                <button type="button" id="saveBtn" class="btn btn-primary">Save  <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                            </div>
                            @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script type="text/javascript">
         $("#state-dropdown").select2({
            placeholder: "Select a state"
        });
        $("#country-dropdown").select2({
            placeholder: "Select a country"
        });
        $("#city-dropdown").select2({
            placeholder: "Select a city"
        });

        $('#companyform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $('#saveBtn').click();
            }
        });

        $('body').on('click', '#saveBtn', function() {
           var btn = $(this);
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#companyform")[0]);

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.company.profile.update') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);

                        if (res.errors.company_name) {
                            $('#company_name-error').show().text(res.errors.company_name);
                        } else {
                            $('#company_name-error').hide();
                        }
                        if (res.errors.gst_in_number) {
                            $('#gst_in_number-error').show().text(res.errors.gst_in_number);
                        } else {
                            $('#gst_in_number-error').hide();
                        }
                        if (res.errors.street_address1) {
                            $('#street_address1-error').show().text(res.errors.street_address1);
                        } else {
                            $('#street_address1-error').hide();
                        }
                        if (res.errors.street_address2) {
                            $('#street_address2-error').show().text(res.errors.street_address2);
                        } else {
                            $('#street_address2-error').hide();
                        }
                        if (res.errors.landmark) {
                            $('#landmark-error').show().text(res.errors.landmark);
                        } else {
                            $('#landmark-error').hide();
                        }
                        if (res.errors.pin_code) {
                            $('#pin_code-error').show().text(res.errors.pin_code);
                        } else {
                            $('#pin_code-error').hide();
                        }
                        if (res.errors.city_id) {
                            $('#city_id-error').show().text(res.errors.city_id);
                        } else {
                            $('#city_id-error').hide();
                        }
                        if (res.errors.state_id) {
                            $('#state_id-error').show().text(res.errors.state_id);
                        } else {
                            $('#state_id-error').hide();
                        }
                        if (res.errors.country_id) {
                            $('#country_id-error').show().text(res.errors.country_id);
                        } else {
                            $('#country_id-error').hide();
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
                        $('#profile_pic-error').html("");
                        $('#company_name-error').html("");
                        $('#city_id-error').html("");
                        $('#gst_in_number-error').html("");
                        $('#landmark-error').html("");
                        $('#pin_code-error').html("");
                        $('#state_id-error').html("");
                        $('#country_id-error').html("");
                        $('#street_address1-error').html("");
                        toastr.success("Company updated successfully!", 'Success', {
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

        $('#country-dropdown').on('change', function() {
            var country_id = $(this).val();
            $("#state-dropdown").html('');
            $.ajax({
                url: "{{ url('get-states-by-country') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#state-dropdown').html('<option value="">Select State</option>');
                    $.each(result.states, function(key, value) {
                        $("#state-dropdown").append('<option data-value="' + value.state_id +
                            '" value="' + value.state_id + '">' + value.state_name + '</option>');
                    });

                    // Trigger the change event on state dropdown after updating options
                    $("#state-dropdown").trigger('change');
                }
            });
        });

        $('#state-dropdown').on('change', function() {
            var state_id = $(this).val();
            $("#city-dropdown").html('');
            $.ajax({
                url: "{{ url('get-cities-by-state') }}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#city-dropdown').html('<option value="">Select City</option>');
                    $.each(result.cities, function(key, value) {
                        $("#city-dropdown").append('<option data-value="' + value.city_id +
                            '" value="' + value.city_id + '">' + value.city_name + '</option>');
                    });
                }
            });
        });
        var id = $('#id').val();
        if(id != ""){
            var country_id = $('#country_id').val();
            var state_id = $('#state_id').val();
            var city_id = $('#city_id').val();
        $('select[name="country_id"]').val(country_id).trigger('change');

            $.ajax({
                url: "{{ url('get-states-by-country') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // Populate state dropdown
                    $('#state-dropdown').html('<option value="">Select State</option>');
                    $.each(result.states, function(key, value) {
                        $("#state-dropdown").append('<option data-value="' + value.state_id +
                            '" value="' + value.state_id + '">' + value.state_name + '</option>');
                    });

                    // Set the selected state in the state dropdown
                    $('#state-dropdown').val(state_id).trigger('change');

                    $.ajax({
                        url: "{{ url('get-cities-by-state') }}",
                        type: "POST",
                        data: {
                            state_id: state_id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            $('#city-dropdown').html('<option value="">Select City</option>');
                            $.each(result.cities, function(key, value) {
                                $("#city-dropdown").append('<option data-value="' + value.city_id +
                                    '" value="' + value.city_id + '">' + value.city_name + '</option>');
                            });

                            // Set the selected city in the city dropdown
                            $('#city-dropdown').val(city_id).trigger('change');
                        }
                    });
                }
            });
        }

        $('#profile_pic').change(function(){
            $('#profile_pic-error').hide();
            var file = this.files[0];
            var fileType = file["type"];
            var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
            if ($.inArray(fileType, validImageTypes) < 0) {
                $('#profile_pic-error').show().text("Please provide a Valid Extension Image(e.g: .jpg .png)");
                var default_image = "{{ asset('image/avatar.png') }}";
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
