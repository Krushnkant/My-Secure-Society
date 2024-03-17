@extends('admin.layout.app')
@section('title', 'Order Payment')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4> Order Payment</h4>
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
                            @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_add(11)))
                                <button type="button" id="AddBtn_OrderPayment" class="btn btn-outline-primary"
                                    data-toggle="modal" data-target="#OrderPaymentModel">Add New</button>
                            @endif
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="orderpaymentTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Pay Amount</th>
                                        <th>Payment Type</th>
                                        <th>Payment Note</th>
                                        <th>Payment Date </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Pay Amount</th>
                                        <th>Payment Type</th>
                                        <th>Payment Note</th>
                                        <th>Payment Date </th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @include('admin.order_payment.addoredit')

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
            $('#orderpaymentTable').DataTable({
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
                    url: "{{ route('admin.orderpayment.listdata') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                    }
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: 1,
                aoColumns: [
                    {
                        width: "5%",
                        data: null,
                        render: function (data, type, row, meta) {
                            // Add serial number column
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        width: "10%",
                        data: 'amount_paid',
                    },
                    {
                        width: "10%",
                        data: 'payment_type',
                        render: function (data) {
                        // Map numerical values to payment types
                        let paymentType = '';
                        let badgeClass = '';
                        let iconClass = '';

                        switch (data) {
                        case 1:
                            paymentType = 'Offline';
                            badgeClass = 'badge badge-warning';
                            iconClass = 'fa fa-wallet'; // Add your offline icon class
                            break;
                        case 2:
                            paymentType = 'Online';
                            badgeClass = 'badge badge-success';
                            iconClass = 'fa fa-globe'; // Add your online icon class
                            break;
                        case 3:
                            paymentType = 'Cheque';
                            badgeClass = 'badge badge-info';
                            iconClass = 'fa fa-money'; // Add your cheque icon class
                            break;
                        default:
                            break;
                    }

                        // Construct the HTML with badge and icon
                        return `<span class="${badgeClass}"><i class="${iconClass}"></i> ${paymentType}</span>`;
                    }
                    },
                    {
                        width: "20%",
                        data: 'payment_note',
                    },
                    {
                        width: "10%",
                        data: 'payment_date',
                    },
                    {
                        data: 'id',
                        width: "5%",
                        orderable: false,
                        render: function(data, type, row) {
                            var is_delete = @json(getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_delete(11)));
                            var action = `<span>`;


                            if (is_delete) {
                                action +=
                                    `<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.order_payment_id}"><i class="fa fa-close color-danger"></i></a>`;
                            }
                            action += `</span>`;
                            return action;
                        }
                    }
                ]
            });
        }

        $('body').on('click', '#AddBtn_OrderPayment', function() {
            $('#OrderPaymentModel').find('.modal-title').html("Add Order Payment");
            $("#OrderPaymentModel").find('form').trigger('reset');
            $('.single-select-placeholder').trigger('change');
            $('#id').val("");
            $('#amount_paid-error').html("");
            $('#payment_date-error').html("");
            $("#OrderPaymentModel").find("#save_newBtn").removeAttr('data-action');
            $("#OrderPaymentModel").find("#save_closeBtn").removeAttr('data-action');
            $("#OrderPaymentModel").find("#save_newBtn").removeAttr('data-id');
            $("#OrderPaymentModel").find("#save_closeBtn").removeAttr('data-id');
            $("#amount_paid").focus();
        });

        $('#orderpaymentform').keypress(function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                save_order_payment($('#save_newBtn'), 'save_new');
            }
        });

        $('body').on('click', '#save_newBtn', function() {
            save_order_payment($(this), 'save_new');
        });

        $('body').on('click', '#save_closeBtn', function() {
            save_order_payment($(this), 'save_close');
        });

        function save_order_payment(btn, btn_type) {
            $(btn).prop('disabled', 1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#orderpaymentform").serializeArray();

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/orderpayment/addorupdate') }}",
                data: formData,
                success: function(res) {
                    if (res.status == 'failed') {
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        if (res.errors.amount_paid) {
                            $('#amount_paid-error').show().text(res.errors.amount_paid);
                        } else {
                            $('#amount_paid-error').hide();
                        }
                        if (res.errors.payment_date) {
                            $('#payment_date-error').show().text(res.errors.payment_date);
                        } else {
                            $('#payment_date-error').hide();
                        }
                    }



                    if (res.status == 200) {
                        if (btn_type == 'save_close') {
                            $("#OrderPaymentModel").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            if (res.action == 'add') {
                                toastr.success("Order Payment added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Order Payment updated successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                        }

                        if (btn_type == 'save_new') {
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled', false);
                            $("#OrderPaymentModel").find('form').trigger('reset');
                            $('.single-select-placeholder').trigger('change');
                            $('#id').val("");
                            $('#amount_paid-error').html("");
                            $('#payment_date-error').html("");
                            $("#OrderPaymentModel").find("#save_newBtn").removeAttr('data-action');
                            $("#OrderPaymentModel").find("#save_closeBtn").removeAttr('data-action');
                            $("#OrderPaymentModel").find("#save_newBtn").removeAttr('data-id');
                            $("#OrderPaymentModel").find("#save_closeBtn").removeAttr('data-id');
                            $("#amount_paid").focus();
                            if (res.action == 'add') {
                                toastr.success("Order Payment added successfully!", 'Success', {
                                    timeOut: 5000
                                });
                            }
                            if (res.action == 'update') {
                                toastr.success("Order Payment updated successfully!", 'Success', {
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
                        $("#OrderPaymentModel").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled', false);
                        toastr.error("Please try again", 'Error', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#OrderPaymentModel").modal('hide');
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
            $('.single-select-placeholder').trigger('change');
            $('#OrderPaymentModel').find('.modal-title').html("Edit Flat");
            $('#payment_date-error').html("");
            $('#amount_paid-error').html("");
            $.get("{{ url('admin/flat') }}" + '/' + edit_id + '/edit', function(data) {
                $('#OrderPaymentModel').find('#save_newBtn').attr("data-action", "update");
                $('#OrderPaymentModel').find('#save_closeBtn').attr("data-action", "update");
                $('#OrderPaymentModel').find('#save_newBtn').attr("data-id", edit_id);
                $('#OrderPaymentModel').find('#save_closeBtn').attr("data-id", edit_id);
                $('#id').val(data.order_payment_id);
                $('#amount_paid').val(data.amount_paid);
                $('#payment_date').val(data.payment_date);
                $('#order_note').val(data.order_note);
                $('select[name="payment_type"]').val(data.payment_type).trigger('change');
                $("#OrderPaymentModel").modal('show');
            });
        });

        $('body').on('click', '#deleteBtn', function() {
            swal({
                title: "Are you sure to delete ?",
                text: "You will not be able to recover this Order !!",
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
                        url: "{{ url('admin/orderpayment') }}" + '/' + remove_id + '/delete',
                        success: function(res) {
                            if (res.status == 200) {
                                toastr.success("payment order deleted successfully!", 'Success', {
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

        $(".single-select-placeholder").select2({
        });

    </script>
@endsection
