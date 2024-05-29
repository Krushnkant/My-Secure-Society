@extends('admin.layout.app')
@section('title', 'Visiting Help Category')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Visiting Help Category</h4>
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
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(15)))
                                <button type="button" id="AddBtn_Category" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#CategoryModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(15)))
                                <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1">
                                    Delete</button>
                            @endif


                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="categoryTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Category Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Category Name</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.visiting_help_category.addoredit')

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
            $('#categoryTable').DataTable({
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
                    url: "{{ route('admin.visitinghelpcategory.listdata') }}",
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
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.visiting_help_category_id}">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'visiting_help_category_name',
                    },
                    {
                        data: 'id',
                        width: "10%",
                        orderable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(15)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(15)));
                            var action = `<span>`;

                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.visiting_help_category_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.visiting_help_category_id}"><i class="fa fa-close color-danger"></i></a>`;
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


                    $('#categoryTable tbody').on('change', '.select-checkbox', function() {
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
                                text: "You will not be able to recover this Category !!",
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
                                        url: "{{ route('admin.visitinghelpcategory.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            if (response.status == 200) {
                                                toastr.success(
                                                "Category deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                                $('#categoryTable').DataTable().clear().draw();
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

        $('body').on('click', '#AddBtn_Category', function() {
            $('#CategoryModal').find('form').attr('action', "{{ url('admin/visitinghelpcategory/add') }}");
            $('#CategoryModal').find('.modal-title').html("Add Visiting Help Category");
            $("#CategoryModal").find('form').trigger('reset');
            $('.single-select-placeholder').trigger('change');
            $('#id').val("");
            $('#visiting_help_category_name-error').html("");
            $("#CategoryModal").find("#save_newBtn").removeAttr('data-action');
            $("#CategoryModal").find("#save_closeBtn").removeAttr('data-action');
            $("#CategoryModal").find("#save_newBtn").removeAttr('data-id');
            $("#CategoryModal").find("#save_closeBtn").removeAttr('data-id');
            $("#visiting_help_category_name").focus();
        });

        $('#categoryform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_category($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_category($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_category($(this), 'save_close');
        });

        function save_category(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#categoryform").serializeArray();
            var formAction = $("#categoryform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.visiting_help_category_name) {
                            $('#visiting_help_category_name-error').show().text(res.errors.visiting_help_category_name);
                        } else {
                            $('#visiting_help_category_name-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#CategoryModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Category added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Category updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#CategoryModal").find('form').trigger('reset');
                            $('.single-select-placeholder').trigger('change');
                            $('#id').val("");
                            $('#visiting_help_category_name-error').html("");
                            $("#CategoryModal").find("#save_newBtn").removeAttr('data-action');
                            $("#CategoryModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#CategoryModal").find("#save_newBtn").removeAttr('data-id');
                            $("#CategoryModal").find("#save_closeBtn").removeAttr('data-id');

                            $("#visiting_help_category_name").focus();
                            if (res.action == 'add') {
                                $('select[name="parent_visiting_help_category_id"]').append('<option value="' + res.newCategoryId + '">' + res.newCategoryName + '</option>');
                                toastr.success("Category added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Category updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }
                        getTableData('', 1);
                    }

                    if (res.status == 400) {
                        $("#CategoryModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#CategoryModal").modal('hide');
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

            $('#CategoryModal').find('.modal-title').html("Edit Visiting Help Category");
            $('#visiting_help_category_name-error').html("");
            $.get("{{ url('admin/visitinghelpcategory') }}" + '/' + edit_id + '/edit', function(data) {
                $('#CategoryModal').find('form').attr('action', "{{ url('admin/visitinghelpcategory/update') }}");
                $('#CategoryModal').find('#save_newBtn').attr("data-action", "update");
                $('#CategoryModal').find('#save_closeBtn').attr("data-action", "update");
                $('#CategoryModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#CategoryModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.visiting_help_category_id);
                $('#visiting_help_category_name').val(data.visiting_help_category_name);
                $("#CategoryModal").modal('show');
            });
        });


        $('body').on('click', '#deleteBtn', function() {
            swal({
                    title: "Are you sure to delete ?",
                    text: "You will not be able to recover this Category !!",
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
                            url: "{{ url('admin/visitinghelpcategory') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("Category deleted successfully!", 'Success', {
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


    </script>
@endsection
