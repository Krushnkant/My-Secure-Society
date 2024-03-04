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
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#right_modal_xl">Add New</button>
                            <button type="button" class="btn btn-outline-danger sweet-ajax1" >Delete</button>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="designationTable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th>Designation Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Designation Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal modal-right fade" id="right_modal_xl" tabindex="-1" role="dialog" aria-labelledby="right_modal_xl">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <form class="form-valide" action="#" method="post">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer modal-footer-fixed px-5">
                                        <button type="button" id="saveNew" class="btn btn-primary">Save & New</button>
                                        <button type="button" id="saveClose" class="btn btn-outline-primary">Save & Close</button>
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
            user_page_tabs('',true);
        });

        $(".table-tab").click(function() {
            var tab_type = $(this).attr('data-tab');
            user_page_tabs(tab_type,true);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function user_page_tabs(tab_type='', is_clearState=false) {
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
                        width: "20%",
                        data: 'designation_name',
                    },
                    {
                        data: 'estatus', // Assume 'status' is the field in your database for the status
                        width: "10%",
                        render: function(data, type, row) {
                            // Add the HTML for the status update switch
                            return `<label class="switch">
                                    <input type="checkbox" data-id="${row.id}" ${data == 1 ? 'checked' : 'demo'}>
                                    <span class="slider"></span>
                            </label>`;
                        }
                    },
                    {
                        data: 'id',
                        width: "5%",
                        render: function(data, type, row) {
                            return `<span>
                                <a href="javascript:void()" class="mr-4" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil color-muted"></i> </a>
                                <a href="javascript:void()" data-toggle="tooltip" data-placement="top" title="Close"><i class="fa fa-close color-danger"></i></a>
                            </span>
                            `; 
                        }
                    }
                ]
            });
        }

        $(".sweet-ajax1").on("click", function() {
            swal({
                title: "Are you sure to delete ?",
                text: "You will not be able to recover this imaginary file !!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it !!",
                closeOnConfirm: !1
            }, function() {
                swal("Deleted !!", "Hey, your imaginary file has been deleted !!", "success")
            })
        });

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

        $(".select2-width-75").select2({
            
        });

        $(".single-select-placeholder").select2({
            placeholder: "Select a state",
            allowClear: true
        });
    </script>
@endsection