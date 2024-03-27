<div class="modal modal-right fade" id="SocietyMemberModal" tabindex="-1" role="dialog" aria-labelledby="SocietyMemberModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form class="form-valide" id="societymemberform" action="#" method="post">
            <input type="hidden" name="society_id" id="society_id" value="{{ $id }}">
            {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Block</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="1">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="form-control"
                                    placeholder="Full Name">
                                <div id="full_name-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Email">
                                    <div id="email-error" class="invalid-feedback animated fadeInDown"
                                        style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="mobile_no" id="mobile_no" class="form-control"
                                        placeholder="Mobile Number">
                                    <div id="mobile_no-error" class="invalid-feedback animated fadeInDown"
                                        style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password">
                                    <div id="password-error" class="invalid-feedback animated fadeInDown"
                                        style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="2" checked> Male
                                    </label>
                                    &nbsp;&nbsp;&nbsp;
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="1"> Female
                                    </label>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Designation <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="designation">
                                    @foreach ($resident_designations as $designation)
                                        <option value="{{ $designation->resident_designation_id }}">
                                            {{ $designation->designation_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Resident Type <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="resident_type">
                                    <option value="1">Tenant</option>
                                    <option value="2">Owner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Blocks <span class="text-danger">*</span></label>
                                <select class="js-states" name="block_id" id="block-dropdown">
                                    <option value=""></option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->society_block_id }}">
                                            {{ $block->block_name }}</option>
                                    @endforeach
                                </select>
                                <div id="block_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Flat <span class="text-danger">*</span></label>
                                <select class=" js-states" name="flat_id_id" id="flat-dropdown">
                                    <option value=""></option>
                                </select>
                                <div id="flat_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
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
