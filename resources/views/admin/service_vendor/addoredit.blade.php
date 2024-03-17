<div class="modal modal-right fade" id="ServiceVendorModal" tabindex="-1" role="dialog" aria-labelledby="ServiceVendorModal">
    <div class="modal-dialog modal-ml" role="document">
        <div class="modal-content">
            <form class="form-valide" id="servicevendorform" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="1">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Vendor<span class="text-danger">*</span></label>
                                <input type="text" name="vendor_company_name" id="vendor_company_name" class="form-control"
                                    placeholder="Vendor">
                                <div id="vendor_company_name-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Service Type <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="service_type">
                                    <option value="1">Delivery</option>
                                    <option value="2">Cab</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group ">
                                <label class="col-form-label" for="file">Logo <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control-file" id="file" onchange=""
                                    name="file">
                                <div id="file-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                <img src="{{ asset('image/avtar.png') }}" class=""
                                    id="file_image_show" height="100px" width="150px"
                                    style="margin-top: 10px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-fixed px-5">
                    <input type="hidden" name="id" id="id">
                    <button type="button" id="save_newBtn" class="btn btn-primary">Save & New <i
                            class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    <button type="button" id="save_closeBtn" class="btn btn-outline-primary">Save & Close <i
                            class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    <button type="button" class="btn btn-light ml-auto" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
