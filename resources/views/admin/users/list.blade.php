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
                            <button type="button" id="AddBtn_User" class="btn btn-outline-primary" data-toggle="modal" data-target="#UserModal">Add New</button>
                            <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1" >Selected Delete</button>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="userTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Full Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Full Name</th>
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

    <script  type="text/javascript">
        $(".single-select-placeholder").select2({
            placeholder: "Select a state",
            allowClear: true
        });
        
        $(document).ready(function() {
            getTableData('',1);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getTableData(tab_type='', is_clearState=false) {
            $('#userTable').DataTable({
                processing: 1,
                serverSide: 1,
                destroy: 1,
                processing: 1,
                "language": {
                    'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                stateSave: function(){
                    if(is_clearState){
                        return false;
                    }
                    else{
                        return 1;
                    }
                },
                ajax: {
                    url: "{{ route('admin.users.listdata') }}",
                    type: "POST",
                    data: function (data) {
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
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.user_id}">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'full_name',
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        orderable: false,
                        render: function(data, type, row) {
                            // Add the HTML for the status update switch
                            return `<label class="switch">
                                    <input type="checkbox" id="statuscheck_${row.user_id}" onchange="changeStatus(${row.user_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
                                    <span class="slider"></span>
                            </label>`;
                        }
                    },
                    {
                        data: 'id',
                        width: "5%",
                        orderable: false,
                        render: function(data, type, row) {
                            return `<span>
                                <a href="#" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.user_id}"><i class="fa fa-pencil color-muted"></i> </a>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Delete" id="deleteBtn" data-id="${row.user_id}"><i class="fa fa-close color-danger"></i></a>
                            </span>
                            `; 
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
                        var allChecked = $('.select-checkbox:checked').length === $('.select-checkbox').length;
                        $('#selectAll').prop('checked', allChecked);
                    });

                    // Example AJAX code for deleting selected rows
                    $('#deleteSelected').on('click', function() {
                        var selectedRows = $('.select-checkbox:checked');
                        if (selectedRows.length === 0) {
                            toastr.error("Please select at least one row to delete.",'Error',{timeOut: 5000});
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
                            url: "{{ route('admin.users.multipledelete') }}",
                            type: "POST",
                            data: { ids: selectedIds },
                            success: function(response) {
                                // Handle success response
                                console.log(response);
                                toastr.success("User Deleted",'Success',{timeOut: 5000});
                                getTableData('',1);
                            },
                            error: function(xhr, status, error) {
                                toastr.error("Please try again",'Error',{timeOut: 5000});
                            }
                        });
                    } 
            });
                    });
         
                }
            });
        }
        
        $('body').on('click', '#deleteBtn', function () {
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
                url: "{{ url('admin/users') }}" +'/' + remove_id +'/delete',
                success: function (res) {
                    if(res.status == 200){
                        toastr.success("User Deleted",'Success',{timeOut: 5000});
                        getTableData('',1);
                    }

                    if(res.status == 400){
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
            } 
            });
        });

        $('body').on('click', '#save_newBtn', function () {
            save_user($(this),'save_new');
        });

        $('body').on('click', '#save_closeBtn', function () {
            save_user($(this),'save_close');
        });

        function save_user(btn,btn_type){
            $(btn).prop('disabled',1);
            $(btn).find('.loadericonfa').show();
            var formData = $("#userform").serializeArray();

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/users/addorupdate') }}",
                data: formData,
                success: function (res) {
                    if(res.status == 'failed'){
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        if (res.errors.full_name) {
                            $('#full_name-error').show().text(res.errors.full_name);
                        } else {
                            $('#full_name-error').hide();
                        }
                    }

                    if(res.status == 200){
                        if(btn_type == 'save_close'){
                            $("#UserModal").modal('hide');
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            if(res.action == 'add'){
                                toastr.success("Designation Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                toastr.success("Designation Updated",'Success',{timeOut: 5000});
                            }
                        }

                        if(btn_type == 'save_new'){
                            $(btn).find('.loadericonfa').hide();
                            $(btn).prop('disabled',false);
                            $("#UserModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#full_name-error').html("");
                            $("#UserModal").find("#save_newBtn").removeAttr('data-action');
                            $("#UserModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#UserModal").find("#save_newBtn").removeAttr('data-id');
                            $("#UserModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#full_name").focus();
                            if(res.action == 'add'){
                                toastr.success("Designation Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                toastr.success("Designation Updated",'Success',{timeOut: 5000});
                            }
                        }
                        getTableData('',1);
                    }

                    if(res.status == 400){
                        $("#UserModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    $("#UserModal").modal('hide');
                    $(btn).find('.loadericonfa').hide();
                    $(btn).prop('disabled',false);
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }


        $("#saveNew").on("click", function () {
            toastr.success("Product has been Updated Successfully!", "Success", {
                timeOut: 500000000,
                closeButton: !0,
                debug: !1,
                newestOnTop: !0,
                progressBar: !0,
                positionClass: "toast-top-right",
                preventDuplicates: !0,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                tapToDismiss: !1
            })
        });

        $('body').on('click', '#AddBtn_User', function () {     
            $('#UserModal').find('.modal-title').html("Add User");
            $("#UserModal").find('form').trigger('reset');
            $('#id').val("");
            $('#full_name-error').html("");
            $("#UserModal").find("#save_newBtn").removeAttr('data-action');
            $("#UserModal").find("#save_closeBtn").removeAttr('data-action');
            $("#UserModal").find("#save_newBtn").removeAttr('data-id');
            $("#UserModal").find("#save_closeBtn").removeAttr('data-id');
            $("#full_name").focus();
        });

        function changeStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/users/changestatus') }}" +'/' + id,
                success: function (res) {
                    if(res.status == 200 && res.action=='deactive'){
                        $("#statuscheck_"+id).val(2);
                        $("#statuscheck_"+id).prop('checked',false);
                        toastr.success("User Deactivated",'Success',{timeOut: 5000});
                    }
                    if(res.status == 200 && res.action=='active'){
                        $("#statuscheck_"+id).val(1);
                        $("#statuscheck_"+id).prop('checked',1);
                        toastr.success("User activated",'Success',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }

        $('body').on('click', '#editBtn', function () {
        var edit_id = $(this).attr('data-id');
       
        $('#UserModal').find('.modal-title').html("Edit User");
        $.get("{{ url('admin/designuserstion') }}" +'/' + edit_id +'/edit', function (data) {
            $('#UserModal').find('#save_newBtn').attr("data-action","update");
            $('#UserModal').find('#save_closeBtn').attr("data-action","update");
            $('#UserModal').find('#save_newBtn').attr("data-id",edit_id);
            $('#UserModal').find('#save_closeBtn').attr("data-id",edit_id);
            $('#id').val(data.user_id);
            $('#full_name').val(data.full_name);
            $("#UserModal").modal('show');
        });
    });


    </script>
@endsection