@extends('admin.layout.app')
@section('title', 'Users')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Users</h4>
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

                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_add(3)) )
                            <button type="button" id="AddBtn_User" class="btn btn-outline-primary" data-toggle="modal" data-target="#UserModal">Add New</button>
                            @endif
                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_delete(3)) )
                             <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="userTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Profile Image</th>
                                        <th>Full Name</th>
                                        <th>Designation</th>
                                        <th>Contact Info</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Profile Image</th>
                                        <th>Full Name</th>
                                        <th>Designation</th>
                                        <th>Contact Info</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.users.addoredit')

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

        $(document).ready(function() {
            getTableData('', 1);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getTableData(tab_type = '', is_clearState = false) {
            $('#userTable').DataTable({
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
                    url: "{{ route('admin.users.listdata') }}",
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
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.user_id}">`;
                        }
                    },
                    {
                        width: "10%",
                        data: 'profile_pic_url',
                        orderable: false,
                        render: function(data, type, row) {
                            var profile_pic = (data != "" && data != null) ? data : '{{ asset("image/avtar.png") }}';
                            return `<div class="media-left">
                                      <img class="media-object mr-3 rounded-circle"  width="50px" height="50px" alt="Profile Pic" src="${profile_pic}" alt="...">
                                </div>`;
                        }
                    },
                    {
                        width: "10%",
                        data: 'full_name',
                    },
                    {
                        width: "10%",
                        data: 'designation',
                    },
                    {
                        width: "15%",
                        data: null,
                        render: function(data, type, row) {
                            return row.email + '<br>' + row.mobile_no;
                        }
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "5%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(3)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.user_id}" onchange="changeStatus(${row.user_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
                                        <span class="slider"></span>
                                </label>`;
                            } else {
                                var statusText = (data == 1) ? 'Active' : 'Inactive';
                                var badgeClass = (data == 1) ? 'success' : 'danger';
                                var estatus = `<span class="badge badge-${badgeClass}">${statusText}</span>`;
                            }
                            return estatus;
                        }
                    },
                    {
                        data: 'id',
                        width: "5%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(3)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(3)));
                            var action =  `<span>`;
                            if(is_edit) {
                              action += `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.user_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if(is_delete) {
                              action += `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.user_id}"><i class="fa fa-close color-danger"></i></a>`;
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

                    $('#userTable tbody').on('change', '.select-checkbox', function() {
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
                                text: "You will not be able to recover this User !!",
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
                                        url: "{{ route('admin.users.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            // Handle success response
                                            console.log(response);
                                            toastr.success("User deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                            // getTableData('', 1);
                                            $('#userTable').DataTable().clear().draw();
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


        $('body').on('click', '#AddBtn_User', function() {
            $('#UserModal').find('form').attr('action', "{{ url('admin/users/add') }}");
            $('#UserModal').find('.modal-title').html("Add User");
            $("#UserModal").find('form').trigger('reset');
            $('.password-field').show();
            $('#id').val("");
            $('#full_name-error').html("");
            $('#email-error').html("");
            $('#mobile_no-error').html("");
            $('.single-select-placeholder').trigger('change');
            $('#password').prop('disabled', false);
            $("#UserModal").find("#save_newBtn").removeAttr('data-action');
            $("#UserModal").find("#save_closeBtn").removeAttr('data-action');
            $("#UserModal").find("#save_newBtn").removeAttr('data-id');
            $("#UserModal").find("#save_closeBtn").removeAttr('data-id');
            $("#full_name").focus();
            var default_image = "{{ asset('image/avtar.png') }}";
            $('#profilepic_image_show').attr('src', default_image);
        });

        $('#userform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_user($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_user($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_user($(this), 'save_close');
        });

        function save_user(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#userform")[0]);
            var formAction = $("#userform").attr('action');
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

                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#UserModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("User added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("User updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#UserModal").find('form').trigger('reset');
                            $('.single-select-placeholder').trigger('change');
                            $('#id').val("");
                            $('#full_name-error').html("");
                            $('#email-error').html("");
                            $('#mobile_no-error').html("");
                            $("#UserModal").find("#save_newBtn").removeAttr('data-action');
                            $("#UserModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#UserModal").find("#save_newBtn").removeAttr('data-id');
                            $("#UserModal").find("#save_closeBtn").removeAttr('data-id');
                            var default_image = "{{ asset('image/avtar.png') }}";
                            $('#profilepic_image_show').attr('src', default_image);
                            $("#full_name").focus();
                            if (res.action == 'add') {
                                toastr.success("User Added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("User Updated successfully!", 'Success', {
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
                        $("#UserModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#UserModal").modal('hide');
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
            $('#UserModal').find('.modal-title').html("Edit User");
            $('#full_name-error').html("");
            $('#email-error').html("");
            $('#mobile_no-error').html("");
            $.get("{{ url('admin/users') }}" + '/' + edit_id + '/edit', function(data) {
                $('#UserModal').find('form').attr('action', "{{ url('admin/users/update') }}");
                $('#UserModal').find('#save_newBtn').attr("data-action", "update");
                $('#UserModal').find('#save_closeBtn').attr("data-action", "update");
                $('#UserModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#UserModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.user_id);
                $('#full_name').val(data.full_name);
                $('#email').val(data.email);
                $('#mobile_no').val(data.mobile_no);
                // $('#password').val(123456);
                // $('#password').prop('disabled', true);
                $('.password-field').hide();
                $('input[name="gender"][value="' + data.gender + '"]').prop('checked', true);
                $('select[name="designation"]').val(data.userdesignation.company_designation_id).trigger('change');
                if(data.profile_pic_url==null){
                    var default_image = "{{ asset('image/avtar.png') }}";
                    $('#profilepic_image_show').attr('src', default_image);
                }
                else{
                    var profile_pic =  data.profile_pic_url;
                    $('#profilepic_image_show').attr('src', profile_pic);
                }
                $("#UserModal").modal('show');
            });
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/users/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("User deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("User activated successfully!", 'Success', {
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
                    text: "You will not be able to recover this User !!",
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
                            url: "{{ url('admin/users') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("User deleted successfully!", 'Success', {
                                        timeOut: 5000
                                    });
                                    getTableData('', 1);
                                }
                                if (res.status == 403) {
                                    toastr.error("Unauthorized", 'Error', {
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

        $('#profile_pic').change(function(){
            $('#profilepic-error').hide();
            var file = this.files[0];
            var fileType = file["type"];
            var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
            if ($.inArray(fileType, validImageTypes) < 0) {
                $('#profilepic-error').show().text("Please provide a Valid Extension Image(e.g: .jpg .png)");
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
