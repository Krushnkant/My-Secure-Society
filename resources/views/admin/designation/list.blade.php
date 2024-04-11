@extends('admin.layout.app')
@section('title', 'Designation')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Designation</h4>
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
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(1)))
                                <button type="button" id="AddBtn_Designation" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#DesignationModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(1)))
                                <button type="button" id="deleteSelected"
                                    class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="designationTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Designation Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Designation Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.designation.addoredit')

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
            $('#designationTable').DataTable({
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
                    url: "{{ route('admin.designation.listdata') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                    }
                },
                order: [
                    [1, 'asc'] // Default ordering by the second column (designation_name) in ascending order
                ],
                pageLength: 10,
                searching: 1,
                aoColumns: [{
                        width: "1%",
                        data: 'id',
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.company_designation_id}">`;
                        }
                    },
                    {
                        width: "74%",
                        data: 'designation_name',
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(1)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.company_designation_id}" onchange="changeStatus(${row.company_designation_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                        width: "15%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_view = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_view(2)));
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(1)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 &&is_delete(1)));

                            var action = `<span>`;
                            if (is_view) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Permission" id="permissionBtn"  data-id="${row.company_designation_id}"><i class="fa fa-lock color-muted"></i> </a>`;
                            }
                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.company_designation_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.company_designation_id}"><i class="fa fa-close color-danger"></i></a>`;
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


                    $('#designationTable tbody').on('change', '.select-checkbox', function() {
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
                                text: "You will not be able to recover this Designation !!",
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
                                        url: "{{ route('admin.designation.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            toastr.success(
                                                "Designation deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                            // getTableData('', 1);
                                            $('#designationTable').DataTable().clear().draw();
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

        $('body').on('click', '#AddBtn_Designation', function() {
            $('#DesignationModal').find('form').attr('action', "{{ url('admin/designation/add') }}");
            $('#DesignationModal').find('.modal-title').html("Add Designation");
            $("#DesignationModal").find('form').trigger('reset');
            $('#id').val("");
            $('#designation_name-error').html("");
            $("#DesignationModal").find("#save_newBtn").removeAttr('data-action');
            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-action');
            $("#DesignationModal").find("#save_newBtn").removeAttr('data-id');
            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-id');
            $("#designation_name").focus();
        });

        $('#designationform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_designation($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_designation($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_designation($(this), 'save_close');
        });

        function save_designation(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#designationform").serializeArray();
            var formAction = $("#designationform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.designation_name) {
                            $('#designation_name-error').show().text(res.errors.designation_name);
                        } else {
                            $('#designation_name-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#DesignationModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Designation added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Designation updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#DesignationModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#designation_name-error').html("");
                            $("#DesignationModal").find("#save_newBtn").removeAttr('data-action');
                            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#DesignationModal").find("#save_newBtn").removeAttr('data-id');
                            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#designation_name").focus();
                            if (res.action == 'add') {
                                toastr.success("Designation added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Designation updated successfully!", 'Success', {
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
                        $("#DesignationModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#DesignationModal").modal('hide');
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
            $('#DesignationModal').find('.modal-title').html("Edit Designation");
            $('#designation_name-error').html("");
            $.get("{{ url('admin/designation') }}" + '/' + edit_id + '/edit', function(data) {
                $('#DesignationModal').find('form').attr('action', "{{ url('admin/designation/update') }}");
                $('#DesignationModal').find('#save_newBtn').attr("data-action", "update");
                $('#DesignationModal').find('#save_closeBtn').attr("data-action", "update");
                $('#DesignationModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#DesignationModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.company_designation_id);
                $('#designation_name').val(data.designation_name);
                $("#DesignationModal").modal('show');
            });
        });



        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/designation/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("Designation deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("Designation activated successfully!", 'Success', {
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
                    text: "You will not be able to recover this Designation !!",
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
                            url: "{{ url('admin/designation') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("Designation deleted successfully!", 'Success', {
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

        $('body').on('click', '#permissionBtn', function(e) {
            // e.preventDefault();
            var id = $(this).attr('data-id');
            var url = "{{ url('admin/designation') }}" + "/" + id + "/permission";
            window.open(url, "_blank");
        });
    </script>
@endsection
