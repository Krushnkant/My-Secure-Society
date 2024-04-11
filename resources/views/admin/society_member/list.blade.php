@extends('admin.layout.app')
@section('title', 'Society Member')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Society Members in {{ $society->society_name }}</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.society.list') }}">Society</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Society Members</a></li>
        </ol>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 btn-page">
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(8)))
                                <button type="button" id="AddBtn_SocietyMember" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#SocietyMemberModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(8)))
                                <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1">
                                    Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="societymemberTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th>Designation</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th>Designation</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.society_member.addoredit')

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
    $(".single-select-placeholder").select2();
    $("#block-dropdown").select2({
        placeholder: "Select a block"
    });
    $("#flat-dropdown").select2({
        placeholder: "Select a flat"
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
            $('#societymemberTable').DataTable({
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
                    url: "{{ route('admin.societymember.listdata') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                        data.society_id = "{{ $id }}";
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
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.society_member_id}">`;
                        }
                    },
                    {
                        width: "10%",
                        data: 'full_name',
                    },
                    {
                        width: "15%",
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return row.email + '<br>' + row.mobile_no;
                        }
                    },
                    {
                        width: "10%",
                        data: 'designation_name'
                    },
                    {
                        width: "15%",
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<b>block : </b> '+ row.block_name + '<br>' + '<b>flat :</b> '+ row.flat_no;
                        }
                    },
                    
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                       className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(8)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.society_member_id}" onchange="changeStatus(${row.society_member_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                        width: "10%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(8)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(8)));
                            var action = `<span>`;
                        
                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.society_member_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.society_member_id}"><i class="fa fa-close color-danger"></i></a>`;
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


                    $('#societymemberTable tbody').on('change', '.select-checkbox', function() {
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
                                text: "You will not be able to recover this Member !!",
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
                                        url: "{{ route('admin.societymember.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            toastr.success(
                                                "Member deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                            // getTableData('', 1);
                                            $('#societymemberTable').DataTable().clear().draw();
                                            $('#selectAll').prop('checked', false);
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

        $('body').on('click', '#AddBtn_SocietyMember', function() {
            $('#SocietyMemberModal').find('form').attr('action', "{{ url('admin/societymember/add') }}");
            $('#SocietyMemberModal').find('.modal-title').html("Add Society Member");
            $("#SocietyMemberModal").find('form').trigger('reset');
            $('#id').val("");
            $("#full_name").focus();
            $('#full_name-error').html("");
            $('#email-error').html("");
            $('#mobile_no-error').html("");
            $('#password-error').html("");
            $('#block_id-error').html("");
            $('#flat_id-error').html("");
            $('.single-select-placeholder').trigger('change');
            $('#block-dropdown').trigger('change');
            $('#flat-dropdown').trigger('change');
            $('#password').prop('disabled', false);
            $("#SocietyMemberModal").find("#save_newBtn").removeAttr('data-action');
            $("#SocietyMemberModal").find("#save_closeBtn").removeAttr('data-action');
            $("#SocietyMemberModal").find("#save_newBtn").removeAttr('data-id');
            $("#SocietyMemberModal").find("#save_closeBtn").removeAttr('data-id');
        });

        $('#societymemberform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_block($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_block($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_block($(this), 'save_close');
        });

        function save_block(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#societymemberform").serializeArray();
            var formAction = $("#societymemberform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
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
                        if (res.errors.password) {
                            $('#password-error').show().text(res.errors.password);
                        } else {
                            $('#password-error').hide();
                        }
                        if (res.errors.block_id) {
                            $('#block_id-error').show().text(res.errors.block_id);
                        } else {
                            $('#block_id-error').hide();
                        }
                        if (res.errors.flat_id) {
                            $('#flat_id-error').show().text(res.errors.flat_id);
                        } else {
                            $('#flat_id-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#SocietyMemberModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Member added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Member updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#SocietyMemberModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#full_name-error').html("");
                            $('#email-error').html("");
                            $('#mobile_no-error').html("");
                            $('#password-error').html("");
                            $('#block_id-error').html("");
                            $('#flat_id-error').html("");
                            $('.single-select-placeholder').trigger('change');
                            $('#block-dropdown').trigger('change');
                            $('#flat-dropdown').trigger('change');
                            $('#password').prop('disabled', false);
                            $("#SocietyMemberModal").find("#save_newBtn").removeAttr('data-action');
                            $("#SocietyMemberModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#SocietyMemberModal").find("#save_newBtn").removeAttr('data-id');
                            $("#SocietyMemberModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#full_name").focus();
                            if (res.action == 'add') {
                                toastr.success("Member added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Member updated successfully!", 'Success', {
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
                        $("#SocietyMemberModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#SocietyMemberModal").modal('hide');
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

            $('#SocietyMemberModal').find('.modal-title').html("Edit Society Member");
            $('#id').val("");
            $('#full_name-error').html("");
            $('#email-error').html("");
            $('#mobile_no-error').html("");
            $('#block_id-error').html("");
            $('#flat_id-error').html("");
            $('.single-select-placeholder').trigger('change');
            $('#block-dropdown').trigger('change');
            $('#flat-dropdown').trigger('change');
            $.get("{{ url('admin/societymember') }}" + '/' + edit_id + '/edit', function(data) {
                $('#SocietyMemberModal').find('form').attr('action', "{{ url('admin/societymember/update') }}");
                $('#SocietyMemberModal').find('#save_newBtn').attr("data-action", "update");
                $('#SocietyMemberModal').find('#save_closeBtn').attr("data-action", "update");
                $('#SocietyMemberModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#SocietyMemberModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.user_id);
                $('#full_name').val(data.full_name);
                $('#email').val(data.email);
                $('#mobile_no').val(data.mobile_no);
                $('#password').val(123456);
                $('#password').prop('disabled', true);
                $('input[name="gender"][value="' + data.gender + '"]').prop('checked', true);
                $('select[name="designation"]').val(data.resident_designation_id).trigger('change');
                $('select[name="resident_type"]').val(data.resident_type).trigger('change');
                $('#block-dropdown').val(data.society_block_id).trigger('change');
                $.ajax({
                    url: "{{ url('admin/get-flat-by-block') }}",
                    type: "POST",
                    data: {
                        block_id: data.society_block_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#flat-dropdown').html('<option value="">Select a flat</option>');
                        $.each(result.flats, function(key, value) {
                            $("#flat-dropdown").append('<option value="' + value.block_flat_id + '">' + value.flat_no + '</option>');
                        });
                        $("#flat-dropdown").val(data.block_flat_id).trigger('change'); // Trigger change event after setting value
                    }
                });
                $("#SocietyMemberModal").modal('show');
            });
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/societymember/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("Member deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("Member activated successfully!", 'Success', {
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
                text: "You will not be able to recover this society member !!",
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
                        url: "{{ url('admin/societymember') }}" + '/' + remove_id + '/delete',
                        success: function(res) {
                            if (res.status == 200) {
                                toastr.success("Member deleted successfully!", 'Success', {
                                    timeOut: 5000
                                });
                                getTableData('', 1);
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

        $('#block-dropdown').on('change', function() {
            var block_id = $(this).val();
            $("#flat-dropdown").html('');
            $.ajax({
                url: "{{ url('admin/get-flat-by-block') }}",
                type: "POST",
                data: {
                    block_id: block_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#flat-dropdown').html('<option value="">Select a flat</option>');
                    $.each(result.flats, function(key, value) {
                        $("#flat-dropdown").append('<option value="' + value.block_flat_id + '">' + value.flat_no + '</option>');
                    });
                    $("#flat-dropdown").trigger('change');
                }
            });
        });
    </script>
@endsection
