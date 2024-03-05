@extends('admin.layout')
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
                            <button type="button" id="AddBtn_Designation" class="btn btn-outline-primary" data-toggle="modal" data-target="#DesignationModal">Add New</button>
                            <button type="button" id="deleteSelected" class="btn btn-outline-danger sweet-ajax1" >Selected Delete</button>
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
                    <div class="modal modal-right fade" id="DesignationModal" tabindex="-1" role="dialog" aria-labelledby="DesignationModal">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <form class="form-valide" id="designationform" action="#" method="post">
                                {{ csrf_field() }}
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Designation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <div class="row">
                                            <div class="col-lg-6 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Designation Name *</label>
                                                    <input type="text" name="designation_name" id="designation_name" class="form-control" placeholder="Designation Name">
                                                    <div id="designation_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer modal-footer-fixed px-5">
                                        <input type="hidden" name="id" id="id">
                                        <button type="button" id="save_newBtn" class="btn btn-primary">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                        <button type="button" id="save_closeBtn" class="btn btn-outline-primary">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                        <button type="button" class="btn btn-light ml-auto" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

        $(document).ready(function() {
            getTableData('',true);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getTableData(tab_type='', is_clearState=false) {
            $('#designationTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                processing: true,
                "language": {
                    'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                stateSave: function(){
                    if(is_clearState){
                        return false;
                    }
                    else{
                        return true;
                    }
                },
                ajax: {
                    url: "{{ route('admin.designation.alldesignationlist') }}",
                    type: "POST",
                    data: function (data) {
                        data.search = $('input[type="search"]').val();
                        data.tab_type = tab_type;
                    }
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: true,
                aoColumns: [
                    {
                        width: "5%",
                        data: 'id',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.company_designation_id}">`;
                        }
                    },
                    {
                        width: "20%",
                        data: 'designation_name',
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        render: function(data, type, row) {
                            // Add the HTML for the status update switch
                            return `<label class="switch">
                                    <input type="checkbox" onchange="chageDesignationStatus(${row.company_designation_id})" value="${data}" ${data == 1 ? 'checked' : ''}>
                                    <span class="slider"></span>
                            </label>`;
                        }
                    },
                    {
                        data: 'id',
                        width: "5%",
                        render: function(data, type, row) {
                            return `<span>
                                <a href="#" class="mr-4" data-toggle="tooltip" title="Edit" id="editBtn"  data-id="${row.company_designation_id}"><i class="fa fa-pencil color-muted"></i> </a>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="delete" id="deleteBtn" data-id="${row.company_designation_id}"><i class="fa fa-close color-danger"></i></a>
                            </span>
                            `; 
                        }
                    }
                ],
                 initComplete: function() {
                    // Handle "Select All" checkbox change event
                    $('#selectAll').on('change', function() {
                        if (this.checked) {
                            $('.select-checkbox').prop('checked', true);
                        } else {
                            $('.select-checkbox').prop('checked', false);
                        }
                    });

                    
                    $('#designationTable tbody').on('change', '.select-checkbox', function() {
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
                            url: "{{ route('admin.designation.multipledelete') }}",
                            type: "POST",
                            data: { ids: selectedIds },
                            success: function(response) {
                                // Handle success response
                                console.log(response);
                                toastr.success("Designation Deleted",'Success',{timeOut: 5000});
                                getTableData('',true);
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
                url: "{{ url('admin/designation') }}" +'/' + remove_id +'/delete',
                success: function (res) {
                    if(res.status == 200){
                        toastr.success("Designation Deleted",'Success',{timeOut: 5000});
                        getTableData('',true);
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
            save_designation($(this),'save_new');
        });

        $('body').on('click', '#save_closeBtn', function () {
            save_designation($(this),'save_close');
        });

        function save_designation(btn,btn_type){
            $(btn).prop('disabled',true);
            $(btn).find('.loadericonfa').show();
            var formData = $("#designationform").serializeArray();

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/addorupdatedesignation') }}",
                data: formData,
                success: function (res) {
                    if(res.status == 'failed'){
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        if (res.errors.designation_name) {
                            $('#designation_name-error').show().text(res.errors.designation_name);
                        } else {
                            $('#designation_name-error').hide();
                        }
                    }

                    if(res.status == 200){
                        if(btn_type == 'save_close'){
                            $("#DesignationModal").modal('hide');
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
                            $("#DesignationModal").find('form').trigger('reset');
                            $('#id').val("");
                            $('#designation_name-error').html("");
                            $("#DesignationModal").find("#save_newBtn").removeAttr('data-action');
                            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-action');
                            $("#DesignationModal").find("#save_newBtn").removeAttr('data-id');
                            $("#DesignationModal").find("#save_closeBtn").removeAttr('data-id');
                            $("#designation_name").focus();
                            if(res.action == 'add'){
                                toastr.success("Designation Added",'Success',{timeOut: 5000});
                            }
                            if(res.action == 'update'){
                                toastr.success("Designation Updated",'Success',{timeOut: 5000});
                            }
                        }
                        getTableData('',true);
                    }

                    if(res.status == 400){
                        $("#DesignationModal").modal('hide');
                        $(btn).find('.loadericonfa').hide();
                        $(btn).prop('disabled',false);
                        toastr.error("Please try again",'Error',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    $("#DesignationModal").modal('hide');
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

        $('body').on('click', '#AddBtn_Designation', function () {     
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

        function chageDesignationStatus(id) {
            $.ajax({
                type: 'GET',
                url: "{{ url('admin/changedesignationstatus') }}" +'/' + id,
                success: function (res) {
                    if(res.status == 200 && res.action=='deactive'){
                        $("#Designationstatuscheck_"+id).val(2);
                        $("#Designationstatuscheck_"+id).prop('checked',false);
                        toastr.success("Designation Deactivated",'Success',{timeOut: 5000});
                    }
                    if(res.status == 200 && res.action=='active'){
                        $("#Designationstatuscheck_"+id).val(1);
                        $("#Designationstatuscheck_"+id).prop('checked',true);
                        toastr.success("Designation activated",'Success',{timeOut: 5000});
                    }
                },
                error: function (data) {
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            });
        }

        $('body').on('click', '#editBtn', function () {
        var edit_id = $(this).attr('data-id');
       
        $('#DesignationModal').find('.modal-title').html("Edit Designation");
        $.get("{{ url('admin/designation') }}" +'/' + edit_id +'/edit', function (data) {
            $('#DesignationModal').find('#save_newBtn').attr("data-action","update");
            $('#DesignationModal').find('#save_closeBtn').attr("data-action","update");
            $('#DesignationModal').find('#save_newBtn').attr("data-id",edit_id);
            $('#DesignationModal').find('#save_closeBtn').attr("data-id",edit_id);
            $('#id').val(data.company_designation_id);
            $('#designation_name').val(data.designation_name);
            $("#DesignationModal").modal('show');
        });

        

    
    });
    </script>
@endsection