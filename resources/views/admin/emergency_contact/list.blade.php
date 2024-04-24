@extends('admin.layout.app')
@section('title', 'Emergency Contact')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Emergency Contact</h4>
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
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(4)))
                                <button type="button" id="AddBtn_EmergencyContact" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#EmergencyContactModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(4)))
                                <button type="button" id="deleteSelected"
                                    class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="emergencycontactTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Help Line For</th>
                                        <th>Help Line No</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Help Line For</th>
                                        <th>Help Line No</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.emergency_contact.addoredit')

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
        $(document).ready(function() {
            getTableData('', 1);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getTableData(tab_type = '', is_clearState = false) {
            $('#emergencycontactTable').DataTable({
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
                    url: "{{ route('admin.emergencycontact.listdata') }}",
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
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.emergency_contact_id }">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'name',
                    },
                    {
                        width: "20%",
                        data: 'mobile_no',
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                       className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(4)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.emergency_contact_id }" onchange="changeStatus(${row.emergency_contact_id })" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(4)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 &&is_delete(4)));

                            var action = `<span>`;

                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.emergency_contact_id }"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.emergency_contact_id }"><i class="fa fa-close color-danger"></i></a>`;
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


                    $('#emergencycontactTable tbody').on('change', '.select-checkbox', function() {
                        // Check if all checkboxes are checked
                        var allChecked = $('.select-checkbox:checked').length === $('.select-checkbox')
                            .length;
                        $('#selectAll').prop('checked', allChecked);
                    });

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
                                text: "You will not be able to recover this Contact !!",
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
                                    $.ajax({
                                        url: "{{ route('admin.emergencycontact.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            toastr.success(
                                                "Emergency Contact deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                            // getTableData('', 1);
                                            $('#emergencycontactTable').DataTable().clear().draw();
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

        $('body').on('click', '#AddBtn_EmergencyContact', function() {
            $('#EmergencyContactModal').find('form').attr('action', "{{ url('admin/emergencycontact/add') }}");
            $('#EmergencyContactModal').find('.modal-title').html("Add Emergency Contact");
            $("#EmergencyContactModal").find('form').trigger('reset');
            $('#id').val("");
            $('#name-error').html("");
            $('#mobile_no-error').html("");
            $("#EmergencyContactModal").find("#save_newBtn").removeAttr('data-action');
            $("#EmergencyContactModal").find("#save_closeBtn").removeAttr('data-action');
            $("#EmergencyContactModal").find("#save_newBtn").removeAttr('data-id');
            $("#EmergencyContactModal").find("#save_closeBtn").removeAttr('data-id');
            $("#name").focus();
        });

        $('#emergencycontactform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_contact($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_contact($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_contact($(this), 'save_close');
        });

        function save_contact(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#emergencycontactform").serializeArray();
            var formAction = $("#emergencycontactform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.name) {
                            $('#name-error').show().text(res.errors.name);
                        } else {
                            $('#name-error').hide();
                        }
                        if (res.errors.mobile_no) {
                            $('#mobile_no-error').show().text(res.errors.mobile_no);
                        } else {
                            $('#mobile_no-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#EmergencyContactModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Emergency Contact added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Emergency Contact updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#EmergencyContactModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#name-error').html("");
                            $('#mobile_no-error').html("");
                            $("#EmergencyContactModal").find("#save_newBtn").removeAttr('data-action');
                            $("#EmergencyContactModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#EmergencyContactModal").find("#save_newBtn").removeAttr('data-id');
                            $("#EmergencyContactModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#name").focus();
                            if (res.action == 'add') {
                                toastr.success("Emergency Contact added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Emergency Contact updated successfully!", 'Success', {
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
                        $("#EmergencyContactModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#EmergencyContactModal").modal('hide');
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
            $('#EmergencyContactModal').find('.modal-title').html("Edit Emergency Contact");
            $('#name-error').html("");
            $('#mobile_no-error').html("");
            $.get("{{ url('admin/emergencycontact') }}" + '/' + edit_id + '/edit', function(data) {
                $('#EmergencyContactModal').find('form').attr('action', "{{ url('admin/emergencycontact/update') }}");
                $('#EmergencyContactModal').find('#save_newBtn').attr("data-action", "update");
                $('#EmergencyContactModal').find('#save_closeBtn').attr("data-action", "update");
                $('#EmergencyContactModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#EmergencyContactModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.emergency_contact_id );
                $('#name').val(data.name);
                $('#mobile_no').val(data.mobile_no);
                $("#EmergencyContactModal").modal('show');
            });
        });



        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/emergencycontact/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("emergency contact deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("emergency contact activated successfully!", 'Success', {
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
                    text: "You will not be able to recover this Contact !!",
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
                            url: "{{ url('admin/emergencycontact') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("emergency contact deleted successfully!", 'Success', {
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
    </script>
@endsection
