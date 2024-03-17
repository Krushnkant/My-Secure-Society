<div class="modal modal-right fade" id="EmergencyContactModal" tabindex="-1" role="dialog" aria-labelledby="EmergencyContactModal">
    <div class="modal-dialog modal-ml" role="document">
        <div class="modal-content">
            <form class="form-valide" id="emergencycontactform" action="#" method="post">
            {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Emergency Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="1">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Name to Help For <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name to Help For">
                                <div id="name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Help Line No <span class="text-danger">*</span></label>
                                <input type="number" name="mobile_no" id="mobile_no" class="form-control" placeholder="Help Line No">
                                <div id="mobile_no-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
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
