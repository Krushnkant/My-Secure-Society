<div class="modal modal-right fade" id="DailyHelpModal" tabindex="-1" role="dialog" aria-labelledby="DailyHelpModal">
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
                                <label class="text-label">Vendor Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="service_name" id="service_name" class="form-control"
                                    placeholder="Vendor Company Name">
                                <div id="service_name-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group ">
                                <label class="col-form-label" for="icon">Icon
                                </label>
                                <input type="file" class="form-control-file" id="icon" onchange=""
                                    name="icon">
                                <div id="icon-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                <img src="{{ asset('image/avtar.png') }}" class=""
                                    id="icon_image_show" height="100px" width="100px"
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
