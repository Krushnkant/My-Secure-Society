@extends('admin.layout')
@section('title', 'Product')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Product</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- <div class="card-header">
                    <h4 class="card-title">Basic Datatable</h4>
                </div> -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 btn-page">
                            <!-- <button type="button" class="btn btn-primary">Add New</button> -->
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#right_modal_xl">Add New</button>
                            <button type="button" class="btn btn-outline-danger sweet-ajax1" >Delete</button>
                            <!-- <button type="button" class="btn btn-secondary"><i class="fa fa-plus color-info"></i></button> -->
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <ul class="nav nav-pills justify-content-end mb-4">
                                <li class=" nav-item">
                                    <a href="#navpills2-1" class="nav-link table-tab active" data-tab="1" data-toggle="tab" aria-expanded="false">Tab One</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#navpills2-2" class="nav-link table-tab" data-tab="3" data-toggle="tab" aria-expanded="false">Tab Two</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#navpills2-3" class="nav-link table-tab" data-tab="2" data-toggle="tab" aria-expanded="true">Tab Three</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datatable" class="display" style="width:100%">
                                <thead class="">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th></th>
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
                                        <h5 class="modal-title">Add Product</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body px-5">
                                        <div class="row">
                                            <div class="col-lg-6 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">First Name*</label>
                                                    <input type="text" name="firstName" class="form-control" placeholder="Parsley" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Last Name*</label>
                                                    <input type="text" name="lastName" class="form-control" placeholder="Montana" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Email Address*</label>
                                                    <div class="input-group">
                                                        <input type="email" class="form-control" id="inputGroupPrepend2" aria-describedby="inputGroupPrepend2" placeholder="example@example.com.com" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Phone Number*</label>
                                                    <div class="input-group">
                                                        <input type="text" name="phoneNumber" class="form-control" placeholder="(+1)408-657-9007" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Where are you from*</label>
                                                    <input type="text" name="place" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Select Category*</label>
                                                    <select class="single-select-placeholder js-states">
                                                        <option value="Alaska">Alaska</option>
                                                        <option value="Hawaii">Hawaii</option>
                                                        <option value="California">California</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12 mb-2">
                                                <div class="form-group">
                                                    <label class="text-label">Select Category*</label>
                                                    <select class="select2-width-75" multiple="multiple" style="width: 75%">
                                                        <option value="AL">Alabama</option>
                                                        <option value="WY">Wyoming</option>
                                                        <option value="AL" selected="selected">Alabama2</option>
                                                        <option value="WY">Wyoming2</option>
                                                        <option value="AL">Alabama3</option>
                                                        <option value="WY">Wyoming3</option>
                                                        <option value="AL">Alabama4</option>
                                                        <option value="WY">Wyoming4</option>
                                                        <option value="AL">Alabama5</option>
                                                        <option value="WY">Wyoming5</option>
                                                    </select>
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
            $('#datatable').DataTable({
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
                    url: "{{ route('products.getProducts') }}",
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
                        data: 'name',
                    },
                    {
                        data: 'description',
                    },
                    {
                        data: 'amount',
                    },
                    {
                        data: 'id',
                        width: "5%",
                        render: function(data, type, row) {
                            return `<a href="#">View</a>`; //you can add your view route here
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