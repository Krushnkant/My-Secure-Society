@extends('admin.layout.app')
@section('title', 'Society')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Society</h4>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 btn-page">

                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(7)))
                                <button type="button" id="AddBtn_Society" class="btn btn-outline-primary" data-toggle="modal"
                                    data-target="#SocietyModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(7)))
                                <button type="button" id="deleteSelected"
                                    class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="societyTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Society Name</th>
                                        <th>Street Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Society Name</th>
                                        <th>Street Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.society.addoredit')

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <!-- Datatable -->
    <script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/js/plugins-init/datatables.init.js') }}"></script>

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

        $(document).ready(function() {
            getTableData('', 1);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getTableData(tab_type = '', is_clearState = false) {
            $('#societyTable').DataTable({
                processing: 1,
                serverSide: 1,
                destroy: 1,
                processing: 1,
                "language": {
                    'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                stateSave: function() {
                    if (is_clearState) {
                        return false;
                    } else {
                        return 1;
                    }
                },
                ajax: {
                    url: "{{ route('admin.society.listdata') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                    }
                },

                order: ['1', 'ASC'],
                pageLength: 10,
                searching: 1,
                aoColumns: [{
                        width: "1%",
                        data: 'id',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.society_id}">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'society_name',
                    },
                    {
                        width: "49%", // Adjust width as needed
                        data: null,
                        render: function(data, type, row) {
                            // Concatenate address fields
                            return `${row.street_address1},${row.landmark}, ${row.city.city_name}, ${row.state.state_name}, ${row.country.country_name}, ${row.pin_code}`;
                        }
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                       className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(7)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.society_id}" onchange="changeStatus(${row.society_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
                                        <span class="slider"></span>
                                </label>`;
                            } else {
                                var statusText = (data == 1) ? 'Active' : 'Inactive';
                                var badgeClass = (data == 1) ? 'success' : 'danger';
                                var estatus =
                                `<span class="badge badge-${badgeClass}">${statusText}</span>`;
                            }
                            return estatus;
                        }
                    },
                    {
                        data: 'id',
                        width: "20%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_view = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_view(8)));
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(7)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(7)));
                            var action = `<span>`;
                                if (is_view) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Society Member" id="viewSocietyMember"  data-id="${row.society_id}"><i class="fa fa-users color-muted"></i> </a>`;
                                }
                            if (is_view) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Society Block" id="viewBlock"  data-id="${row.society_id}"><i class="fa fa-list color-muted"></i> </a>`;
                                }
                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.society_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.society_id}"><i class="fa fa-close color-danger"></i></a>`;
                            }
                            action += `</span>`;
                            return action;
                        }
                    }
                ],
                initComplete: function() {
                    // Handle "Select All" checkbox change event
                    $('#selectAll').on('change', function() {
                        if (this.checked) {
                            $('.select-checkbox').prop('checked', 1);
                        } else {
                            $('.select-checkbox').prop('checked', false);
                        }
                    });

                    $('#societyTable tbody').on('change', '.select-checkbox', function() {
                        // Check if all checkboxes are checked
                        var allChecked = $('.select-checkbox:checked').length === $('.select-checkbox')
                            .length;
                        $('#selectAll').prop('checked', allChecked);
                    });

                    // Example AJAX code for deleting selected rows
                    $('#deleteSelected').off('click').on('click', function() {
                        var selectedRows = $('.select-checkbox:checked');
                        if (selectedRows.length === 0) {
                            toastr.error("Please select at least one row to delete.", 'Error', {
                                timeOut: 5000
                            });
                            return;
                        }
                        var selectedIds = [];
                        swal({
                                title: "Are you sure to delete ?",
                                text: "You will not be able to recover this Society !!",
                                type: "warning",
                                showCancelButton: !0,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes, delete it !!",
                                closeOnConfirm: !1,
                                closeOnCancel: !0
                            })
                            .then((willDelete) => {
                                if (willDelete.value) {

                                    $('.select-checkbox:checked').each(function() {
                                        selectedIds.push($(this).data('id'));
                                    });

                                    // Perform AJAX request to delete selected rows
                                    $.ajax({
                                        url: "{{ route('admin.society.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {

                                            if (response.status == 200) {
                                                toastr.success(
                                                "Society deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                                $('#societyTable').DataTable().clear().draw();
                                                $('#selectAll').prop('checked', false);
                                            }
                                            if (response.status == 300) {
                                                toastr.error(response.message, 'Error', {
                                                    timeOut: 5000
                                                });
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            toastr.error("Please try again", 'Error', {
                                                timeOut: 5000
                                            });
                                        }
                                    });
                                }
                            });
                    });

                }
            });
        }

        $('#societyform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_society($('#save_newBtn'), 'save_new');
            }
        });


        $('body').on('click', '#AddBtn_Society', function() {
            $('#SocietyModal').find('form').attr('action', "{{ url('admin/society/add') }}");
            $('#SocietyModal').find('.modal-title').html("Add Society");
            $("#SocietyModal").find('form').trigger('reset');
            $('#id').val("");
            $('#society_name-error').html("");
            $('#street_address1-error').html("");
            $('#street_address2-error').html("");
            $('#landmark-error').html("");
            $('#pin_code-error').html("");
            $('#latitude-error').html("");
            $('#longitude-error').html("");
            $('#city_id-error').html("");
            $('#state_id-error').html("");
            $('#country_id-error').html("");
            $('#country-dropdown').trigger('change');
            $('#state-dropdown').trigger('change');
            $('#city-dropdown').trigger('change');
            $("#SocietyModal").find("#save_newBtn").removeAttr('data-action');
            $("#SocietyModal").find("#save_closeBtn").removeAttr('data-action');
            $("#SocietyModal").find("#save_newBtn").removeAttr('data-id');
            $("#SocietyModal").find("#save_closeBtn").removeAttr('data-id');
            $("#society_name").focus();
        });

        $('body').on('click', '#save_newBtn', function() {
            save_society($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_society($(this), 'save_close');
        });

        function save_society(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#societyform")[0]);
            var formAction = $("#societyform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);

                        if (res.errors.society_name) {
                            $('#society_name-error').show().text(res.errors.society_name);
                        } else {
                            $('#society_name-error').hide();
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
                        if (res.errors.latitude) {
                            $('#latitude-error').show().text(res.errors.latitude);
                        } else {
                            $('#latitude-error').hide();
                        }
                        if (res.errors.longitude) {
                            $('#longitude-error').show().text(res.errors.longitude);
                        } else {
                            $('#longitude-error').hide();
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


                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#SocietyModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Society added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Society updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#SocietyModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#society_name-error').html("");
                            $('#street_address1-error').html("");
                            $('#street_address2-error').html("");
                            $('#landmark-error').html("");
                            $('#pin_code-error').html("");
                            $('#latitude-error').html("");
                            $('#longitude-error').html("");
                            $('#city_id-error').html("");
                            $('#state_id-error').html("");
                            $('#country_id-error').html("");
                            $('#country-dropdown').trigger('change');
                            $('#state-dropdown').trigger('change');
                            $('#city-dropdown').trigger('change');
                            $("#SocietyModal").find("#save_newBtn").removeAttr('data-action');
                            $("#SocietyModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#SocietyModal").find("#save_newBtn").removeAttr('data-id');
                            $("#SocietyModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#society_name").focus();
                            if (res.action == 'add') {
                                toastr.success("Society Added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Society Updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }
                        getTableData('', 1);
                    }

                    if (res.status == 300) {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error(res.message, 'Error', {
                            timeOut: 5000
                        });
                    }

                    if (res.status == 400) {
                        $("#SocietyModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#SocietyModal").modal('hide');
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        }


        $('body').on('click', '#editBtn', function() {
            var edit_id = $(this).attr('data-id');
            $('#SocietyModal').find('.modal-title').html("Edit Society");
            $('#society_name-error').html("");
            $('#street_address1-error').html("");
            $('#street_address2-error').html("");
            $('#landmark-error').html("");
            $('#pin_code-error').html("");
            $('#latitude-error').html("");
            $('#longitude-error').html("");
            $('#city_id-error').html("");
            $('#state_id-error').html("");
            $('#country_id-error').html("");

            $.get("{{ url('admin/society') }}" + '/' + edit_id + '/edit', function(data) {
                $('#SocietyModal').find('form').attr('action', "{{ url('admin/society/update') }}");
                $('#SocietyModal').find('#save_newBtn').attr("data-action", "update");
                $('#SocietyModal').find('#save_closeBtn').attr("data-action", "update");
                $('#SocietyModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#SocietyModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.society_id);
                $('#society_name').val(data.society_name);
                $('#street_address1').val(data.street_address1);
                $('#street_address2').val(data.street_address2);
                $('#landmark').val(data.landmark);
                $('#pin_code').val(data.pin_code);
                $('#latitude').val(data.latitude);
                $('#longitude').val(data.longitude);

                $('select[name="country_id"]').val(data.country_id).trigger('change');

                $.ajax({
                    url: "{{ url('get-states-by-country') }}",
                    type: "POST",
                    data: {
                        country_id: data.country_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        // Populate state dropdown
                        $.each(result.states, function(key, value) {
                            $("#state-dropdown").append('<option data-value="' + value.state_id +
                                '" value="' + value.state_id + '">' + value.state_name + '</option>');
                        });

                        // Set the selected state in the state dropdown
                        $('#state-dropdown').val(data.state_id).trigger('change');

                        $.ajax({
                            url: "{{ url('get-cities-by-state') }}",
                            type: "POST",
                            data: {
                                state_id: data.state_id,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                // Populate city dropdown
                                $.each(result.cities, function(key, value) {
                                    $("#city-dropdown").append('<option data-value="' + value.city_id +
                                        '" value="' + value.city_id + '">' + value.city_name + '</option>');
                                });
                                $('#city-dropdown').val(data.city_id).trigger('change');
                            }
                        });
                    }
                });
                $("#SocietyModal").modal('show');
            });
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/society/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("Society deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("Society activated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
                }
            });
        }

        $('body').on('click', '#deleteBtn', function() {
            swal({
                    title: "Are you sure to delete ?",
                    text: "You will not be able to recover this Society !!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it !!",
                    closeOnConfirm: !1,
                    closeOnCancel: !0
                })
                .then((willDelete) => {
                    if (willDelete.value) {
                        var remove_id = $(this).attr('data-id');
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('admin/society') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("User deleted successfully!", 'Success', {
                                        timeOut: 5000
                                    });
                                    getTableData('', 1);
                                }
                                if (res.status == 300) {
                                    toastr.error(res.message, 'Error', {
                                        timeOut: 5000
                                    });
                                }

                                if (res.status == 400) {
                                    toastr.error("Please try again", 'Error', {
                                        timeOut: 5000
                                    });
                                }
                            },
                            error: function(data) {
                                toastr.error("Please try again", 'Error', {
                                    timeOut: 5000
                                });
                            }
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

        $('body').on('click', '#viewBlock', function(e) {
            // e.preventDefault();
            var id = $(this).attr('data-id');
            var url = "{{ url('admin/block') }}" + "/" + id;
            window.open(url, "_blank");
        });

        $('body').on('click', '#viewSocietyMember', function(e) {
            // e.preventDefault();
            var id = $(this).attr('data-id');
            var url = "{{ url('admin/societymember') }}" + "/" + id;
            window.open(url, "_blank");
        });


    </script>
@endsection
