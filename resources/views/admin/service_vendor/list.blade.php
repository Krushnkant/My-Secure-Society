@extends('admin.layout.app')
@section('title', 'Service Vendor')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Service Vendor</h4>
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

                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_add(13)) )
                            <button type="button" id="AddBtn_ServiceVendor" class="btn btn-outline-primary" data-toggle="modal" data-target="#ServiceVendorModal">Add New</button>
                            @endif
                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_delete(13)) )
                             <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="serviceVendorTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Vendor</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Vendor</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.service_vendor.addoredit')

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
            $('#serviceVendorTable').DataTable({
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
                    url: "{{ route('admin.servicevendor.listdata') }}",
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
                        width: "5%",
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.service_vendor_id}">`;
                        }
                    },
                    {
                        width: "10%",
                        data: 'vendor_company_name',
                    },
                    {
                        width: "10%",
                        data: 'service_type',
                        orderable: false,
                        render: function(data, type, row) {
                            switch (data) {
                                case 1:
                                    return '<span class="badge badge-success">Delivery</span>';
                                case 2:
                                    return '<span class="badge badge-success">Cab</span>';
                                default:
                                    return '';
                            }
                        }
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(13)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.service_vendor_id}" onchange="changeStatus(${row.service_vendor_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(13)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(13)));
                            var action =  `<span>`;
                            if(is_edit) {
                              action += `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.service_vendor_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if(is_delete) {
                              action += `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.service_vendor_id}"><i class="fa fa-close color-danger"></i></a>`;
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

                    $('#serviceVendorTable tbody').on('change', '.select-checkbox', function() {
                        // Check if all checkboxes are checked
                        var allChecked = $('.select-checkbox:checked').length === $('.select-checkbox')
                            .length;
                        $('#selectAll').prop('checked', allChecked);
                    });

                    // Example AJAX code for deleting selected rows
                    $('#deleteSelected').on('click', function() {
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
                                text: "You will not be able to recover this Vendor !!",
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
                                        url: "{{ route('admin.servicevendor.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            // Handle success response
                                            console.log(response);
                                            toastr.success("Vendor deleted successfully!",
                                                'Success', {
                                                    timeOut: 5000
                                                });
                                            getTableData('', 1);
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


        $('body').on('click', '#AddBtn_ServiceVendor', function() {
            $('#ServiceVendorModal').find('.modal-title').html("Add vendor");
            $("#ServiceVendorModal").find('form').trigger('reset');
            $('#id').val("");
            $('#vendor_company_name-error').html("");
            $('#file-error').html("");
            $('.single-select-placeholder').trigger('change');
            $("#ServiceVendorModal").find("#save_newBtn").removeAttr('data-action');
            $("#ServiceVendorModal").find("#save_closeBtn").removeAttr('data-action');
            $("#ServiceVendorModal").find("#save_newBtn").removeAttr('data-id');
            $("#ServiceVendorModal").find("#save_closeBtn").removeAttr('data-id');
            $("#vendor_company_name").focus();
            var default_image = "{{ asset('image/placeholder.png') }}";
            $('#file_image_show').attr('src', default_image);
        });

        $('#servicevendorform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_servicevendor($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_servicevendor($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_servicevendor($(this), 'save_close');
        });

        function save_servicevendor(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#servicevendorform")[0]);

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/servicevendor/addorupdate') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.vendor_company_name) {
                            $('#vendor_company_name-error').show().text(res.errors.vendor_company_name);
                        } else {
                            $('#vendor_company_name-error').hide();
                        }
                        if (res.errors.file) {
                            $('#file-error').show().text(res.errors.file);
                        } else {
                            $('#file-error').hide();
                        }
                    }
                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#ServiceVendorModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Vendor added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Vendor updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#ServiceVendorModal").find('form').trigger('reset');
                            $('.single-select-placeholder').trigger('change');
                            $('#id').val("");
                            $('#vendor_company_name-error').html("");
                            $('#file-error').html("");
                            $("#ServiceVendorModal").find("#save_newBtn").removeAttr('data-action');
                            $("#ServiceVendorModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#ServiceVendorModal").find("#save_newBtn").removeAttr('data-id');
                            $("#ServiceVendorModal").find("#save_closeBtn").removeAttr('data-id');
                            var default_image = "{{ asset('image/placeholder.png') }}";
                            $('#file_image_show').attr('src', default_image);
                            $("#vendor_company_name").focus();
                            if (res.action == 'add') {
                                toastr.success("Vendor Added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Vendor Updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }
                        getTableData('', 1);
                    }

                    if (res.status == 400) {
                        $("#ServiceVendorModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#ServiceVendorModal").modal('hide');
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
            $('#ServiceVendorModal').find('.modal-title').html("Edit Vendor");
            $('#vendor_company_name-error').html("");
            $('#file-error').html("");
            $.get("{{ url('admin/servicevendor') }}" + '/' + edit_id + '/edit', function(data) {
                $('#ServiceVendorModal').find('#save_newBtn').attr("data-action", "update");
                $('#ServiceVendorModal').find('#save_closeBtn').attr("data-action", "update");
                $('#ServiceVendorModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#ServiceVendorModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.service_vendor_id);
                $('#vendor_company_name').val(data.vendor_company_name);
                $('select[name="service_type"]').val(data.service_type).trigger('change');
                if(data.service_vendor_file.file_url==null){
                    var default_image = "{{ asset('image/placeholder.png') }}";
                    $('#file_image_show').attr('src', default_image);
                }
                else{
                    var file_pic =  data.service_vendor_file.file_url;
                    $('#file_image_show').attr('src', file_pic);
                }
                $("#ServiceVendorModal").modal('show');
            });
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/servicevendor/changestatus') }}" + '/' + id,
                success: function(res) {
                    if (res.status == 200 && res.action == 'deactive') {
                        $("#statuscheck_" + id).val(2);
                        $("#statuscheck_" + id).prop('checked', false);
                        toastr.success("Vendor deactivated successfully!", 'Success', {
                            timeOut: 5000
                        });
                    }
                    if (res.status == 200 && res.action == 'active') {
                        $("#statuscheck_" + id).val(1);
                        $("#statuscheck_" + id).prop('checked', 1);
                        toastr.success("Vendor activated successfully!", 'Success', {
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
                    text: "You will not be able to recover this Vendor !!",
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
                            url: "{{ url('admin/servicevendor') }}" + '/' + remove_id + '/delete',
                            success: function(res) {
                                if (res.status == 200) {
                                    toastr.success("Vendor deleted successfully!", 'Success', {
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

        $('#file').change(function(){
            $('#file-error').hide();
            var file = this.files[0];
            var fileType = file["type"];
            var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
            if ($.inArray(fileType, validImageTypes) < 0) {
                $('#file-error').show().text("Please provide a Valid Extension Image(e.g: .jpg .png)");
                var default_image = "{{ asset('image/placeholder.png') }}";
                $('#file_image_show').attr('src', default_image);
            }
            else {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#file_image_show').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
@endsection
