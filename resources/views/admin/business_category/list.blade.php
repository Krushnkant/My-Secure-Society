@extends('admin.layout.app')
@section('title', 'Business Category')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Business Category</h4>
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
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(5)))
                                <button type="button" id="AddBtn_BusinessCategory" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#BusinessCategoryModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(5)))
                                <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1">
                                    Delete</button>
                            @endif


                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="businesscategoryTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Category Name</th>
                                        <th>Parent Category</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Category Name</th>
                                        <th>Parent Category</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.business_category.addoredit')

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
            $('#businesscategoryTable').DataTable({
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
                    url: "{{ route('admin.businesscategory.listdata') }}",
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
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.business_category_id}">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'business_category_name',
                    },
                    {
                        width: "20%",
                        data: 'parent_category',
                        render: function(data, type, row) {
                            return data ? data.business_category_name : '-';
                        }
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                       className: 'text-center',
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(5)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.business_category_id}" onchange="changeStatus(${row.business_category_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(5)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(5)));
                            var action = `<span>`;

                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.business_category_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.business_category_id}"><i class="fa fa-close color-danger"></i></a>`;
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


                    $('#businesscategoryTable tbody').on('change', '.select-checkbox', function() {
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
                                        url: "{{ route('admin.businesscategory.multipledelete') }}",
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
                                                $('#businesscategoryTable').DataTable().clear().draw();
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

        $('body').on('click', '#AddBtn_BusinessCategory', function() {
            $('#BusinessCategoryModal').find('form').attr('action', "{{ url('admin/businesscategory/add') }}");
            $('#BusinessCategoryModal').find('.modal-title').html("Add Business Category");
            $("#BusinessCategoryModal").find('form').trigger('reset');
            $('.single-select-placeholder').trigger('change');
            $('#id').val("");
            $('#business_category_name-error').html("");
            $("#BusinessCategoryModal").find("#save_newBtn").removeAttr('data-action');
            $("#BusinessCategoryModal").find("#save_closeBtn").removeAttr('data-action');
            $("#BusinessCategoryModal").find("#save_newBtn").removeAttr('data-id');
            $("#BusinessCategoryModal").find("#save_closeBtn").removeAttr('data-id');
            $("#business_category_name").focus();

            $.get("{{ route('admin.businesscategory.ajaxlist') }}", function(data) {
                var categories = data.categories;
                var dropdown = $('select[name="parent_business_category_id"]');
                dropdown.empty().append('<option value=""></option>');
                $.each(categories, function(index, category) {
                    dropdown.append('<option value="' + category.business_category_id + '">' + category.business_category_name + '</option>');
                });
            });
        });

        $('#businesscategoryform').keypress(function(event) {
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
            var formData = $("#businesscategoryform").serializeArray();
            var formAction = $("#businesscategoryform").attr('action');
            $.ajax({
                type: 'POST',
                url: formAction,
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.business_category_name) {
                            $('#business_category_name-error').show().text(res.errors.business_category_name);
                        } else {
                            $('#business_category_name-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#BusinessCategoryModal").modal('hide');
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
                            $("#BusinessCategoryModal").find('form').trigger('reset');
                            $('.single-select-placeholder').trigger('change');
                            $('#id').val("");
                            $('#business_category_name-error').html("");
                            $("#BusinessCategoryModal").find("#save_newBtn").removeAttr('data-action');
                            $("#BusinessCategoryModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#BusinessCategoryModal").find("#save_newBtn").removeAttr('data-id');
                            $("#BusinessCategoryModal").find("#save_closeBtn").removeAttr('data-id');

                            $("#business_category_name").focus();
                            if (res.action == 'add') {
                                $('select[name="parent_business_category_id"]').append('<option value="' + res.newCategoryId + '">' + res.newCategoryName + '</option>');
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
                        $("#BusinessCategoryModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#BusinessCategoryModal").modal('hide');
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

            $('#BusinessCategoryModal').find('.modal-title').html("Edit Business Category");
            $('#business_category_name-error').html("");
            $.get("{{ url('admin/businesscategory') }}" + '/' + edit_id + '/edit', function(data) {
                $('#BusinessCategoryModal').find('form').attr('action', "{{ url('admin/businesscategory/update') }}");
                $('#BusinessCategoryModal').find('#save_newBtn').attr("data-action", "update");
                $('#BusinessCategoryModal').find('#save_closeBtn').attr("data-action", "update");
                $('#BusinessCategoryModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#BusinessCategoryModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.business_category_id);
                $('#business_category_name').val(data.business_category_name);
                $.get("{{ route('admin.businesscategory.ajaxlist') }}" + '/' + data.business_category_id, function(datacat) {
                    var categories = datacat.categories;
                    var dropdown = $('select[name="parent_business_category_id"]');
                    dropdown.empty().append('<option value=""></option>');
                    $.each(categories, function(index, category) {
                        dropdown.append('<option value="' + category.business_category_id + '">' + category.business_category_name + '</option>');
                    });
                    dropdown.val(data.parent_business_category_id).trigger('change');

                });


                $("#BusinessCategoryModal").modal('show');
            });
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/businesscategory/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("Category deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("Category activated successfully!", 'Success', {
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
                            url: "{{ url('admin/businesscategory') }}" + '/' + remove_id + '/delete',
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

        $(".single-select-placeholder").select2({
            placeholder: "Select a parent category",
            allowClear: true
        });
    </script>
@endsection
