@extends('admin.layout.app')
@section('title', 'Order')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Order</h4>
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

                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(10)))
                                <button type="button" id="AddBtn_Order" class="btn btn-outline-primary" data-toggle="modal"
                                    data-target="#OrderModal">Add New</button>
                            @endif
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(10)))
                                <button type="button" id="deleteSelected"
                                    class="btn btn-outline-danger sweet-ajax1">Delete</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="ordertable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Order Id</th>
                                        <th>Society Name</th>
                                        <th>Sub Total</th>
                                        <th>GST Percent</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid Amount</th>
                                        <th>Total Outstanding Amount</th>
                                        <th>Order Status</th>
                                        <th>Due Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Order Id</th>
                                        <th>Society Name</th>
                                        <th>Sub Total</th>
                                        <th>GST Percent</th>
                                        <th>Total Amount</th>
                                        <th>Total Paid Amount</th>
                                        <th>Total Outstanding Amount</th>
                                        <th>Order Status</th>
                                        <th>Due Date</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.subscription_order.addoredit')

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
        $("#society-dropdown").select2({
            placeholder: "Select a society"
        });
        $("#order-status-dropdown").select2({
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
            $('#ordertable').DataTable({
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
                    url: "{{ route('admin.subscriptionorder.listdata') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                    }
                },

                order: ['1', 'DESC'],
                pageLength: 10,
                searching: 1,
                aoColumns: [{
                        width: "5%",
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.subscription_order_id}">`;
                        }
                    },
                    {
                        width: "10%",
                        data: 'order_id',
                    },
                    {
                        width: "10%",
                        data: 'society_id',
                    },
                    {
                        width: "10%",
                        data: 'sub_total_amount',
                    },
                    {
                        width: "10%",
                        data: 'gst_percent',
                    },
                    {
                        width: "10%",
                        data: 'total_amount',
                    },
                    {
                        width: "10%",
                        data: 'total_paid_amount',
                    },
                    {
                        width: "10%",
                        data: 'total_outstanding_amount',
                    },
                    {
                        data: 'order_status', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(10)));
                            if (is_edit) {
                                var estatus = `<label class="switch">
                                        <input type="checkbox" id="statuscheck_${row.subscription_order_id}" onchange="changeStatus(${row.subscription_order_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
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
                        width: "10%",
                        data: 'due_date',
                    },
                    {
                        data: 'id',
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_view = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_view(11)));
                            var is_edit = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(10)));
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(10)));
                            var action = `<span>`;
                            if (is_view) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Order Payment" id="viewOrderPayment"  data-id="${row.subscription_order_id}"><i class="fa fa-list color-muted"></i> </a>`;
                                }    
                            if (is_edit) {
                                action +=
                                    `<a href="javascript:void(0);" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.subscription_order_id}"><i class="fa fa-pencil color-muted"></i> </a>`;
                            }
                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.subscription_order_id}"><i class="fa fa-close color-danger"></i></a>`;
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

                    $('#ordertable tbody').on('change', '.select-checkbox', function() {
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
                                text: "You will not be able to recover this imaginary file !!",
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
                                        url: "{{ route('admin.subscriptionorder.multipledelete') }}",
                                        type: "POST",
                                        data: {
                                            ids: selectedIds
                                        },
                                        success: function(response) {
                                            // Handle success response
                                            console.log(response);
                                            toastr.success(
                                                "Order deleted successfully!",
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


        $('body').on('click', '#AddBtn_Order', function() {
            $('#OrderModal').find('.modal-title').html("Add Society");
            $("#OrderModal").find('form').trigger('reset');
            $('#id').val("");
            $('#society_name-error').html("");
            $('#street_address1-error').html("");
            $('#landmark-error').html("");
            $('#pin_code-error').html("");
            $('#city_id-error').html("");
            $('#state_id-error').html("");
            $('#country_id-error').html("");
            $('#country-dropdown').trigger('change');
            $('#state-dropdown').trigger('change');
            $('#city-dropdown').trigger('change');
            $("#OrderModal").find("#save_newBtn").removeAttr('data-action');
            $("#OrderModal").find("#save_closeBtn").removeAttr('data-action');
            $("#OrderModal").find("#save_newBtn").removeAttr('data-id');
            $("#OrderModal").find("#save_closeBtn").removeAttr('data-id');
            $("#society_name").focus();
        });

        $('body').on('click', '#save_newBtn', function() {
            save_order($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_order($(this), 'save_close');
        });

        function save_order(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = new FormData($("#orderform")[0]);

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/subscriptionorder/addorupdate') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        
                        if (res.errors.total_flat) {
                            $('#total_flat-error').show().text(res.errors.total_flat);
                        } else {
                            $('#total_flat-error').hide();
                        }
                        if (res.errors.amount_per_flat) {
                            $('#amount_per_flat-error').show().text(res.errors.amount_per_flat);
                        } else {
                            $('#amount_per_flat-error').hide();
                        }
                        if (res.errors.sub_total_amount) {
                            $('#sub_total_amount-error').show().text(res.errors.sub_total_amount);
                        } else {
                            $('#sub_total_amount-error').hide();
                        }
                        if (res.errors.gst_percent) {
                            $('#gst_percent-error').show().text(res.errors.gst_percent);
                        } else {
                            $('#gst_percent-error').hide();
                        }
                        if (res.errors.gst_amount) {
                            $('#gst_amount-error').show().text(res.errors.gst_amount);
                        } else {
                            $('#gst_amount-error').hide();
                        }
                        if (res.errors.total_paid_amount) {
                            $('#total_paid_amount-error').show().text(res.errors.total_paid_amount);
                        } else {
                            $('#total_paid_amount-error').hide();
                        }
                        if (res.errors.total_outstanding_amount) {
                            $('#total_outstanding_amount-error').show().text(res.errors.total_outstanding_amount);
                        } else {
                            $('#total_outstanding_amount-error').hide();
                        }
                        if (res.errors.due_date) {
                            $('#due_date-error').show().text(res.errors.due_date);
                        } else {
                            $('#due_date-error').hide();
                        }
                    }

                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#OrderModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("order added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("order updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#OrderModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#total_flat-error').html("");
                            $('#society-dropdown').trigger('change');
                            $("#OrderModal").find("#save_newBtn").removeAttr('data-action');
                            $("#OrderModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#OrderModal").find("#save_newBtn").removeAttr('data-id');
                            $("#OrderModal").find("#save_closeBtn").removeAttr('data-id');
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

                    if (res.status == 400) {
                        $("#OrderModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#OrderModal").modal('hide');
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
            $('#OrderModal').find('.modal-title').html("Edit Order");
            $('#society_name-error').html("");
            $('#street_address1-error').html("");
            $('#landmark-error').html("");
            $('#pin_code-error').html("");
            $('#city_id-error').html("");
            $('#state_id-error').html("");
            $('#country_id-error').html("");
         
            $.get("{{ url('admin/subscriptionorder') }}" + '/' + edit_id + '/edit', function(data) {
                $('#OrderModal').find('#save_newBtn').attr("data-action", "update");
                $('#OrderModal').find('#save_closeBtn').attr("data-action", "update");
                $('#OrderModal').find('#save_newBtn').attr("data-id", edit_id);
                $('#OrderModal').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.subscription_order_id);
                $('#society_name').val(data.society_name);
                $('#street_address1').val(data.street_address1);
                $('#street_address2').val(data.street_address2);
                $('#landmark').val(data.landmark);
                $('#pin_code').val(data.pin_code);
                 
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
                        $('#state-dropdown').html('<option value="">Select State</option>');
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
                                $('#city-dropdown').html('<option value="">Select City</option>');
                                $.each(result.cities, function(key, value) {
                                    $("#city-dropdown").append('<option data-value="' + value.city_id +
                                        '" value="' + value.city_id + '">' + value.city_name + '</option>');
                                });

                                // Set the selected city in the city dropdown
                                $('#city-dropdown').val(data.city_id).trigger('change');
                            }
                        });
                    }
                });
                $("#OrderModal").modal('show');
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
                    text: "You will not be able to recover this imaginary file !!",
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

       
    </script>
@endsection
