<div class="modal modal-right fade" id="SocietyModal" tabindex="-1" role="dialog" aria-labelledby="SocietyModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-valide" id="userform" method="post">
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
                                <label class="text-label" for="society_name">Society Name <span class="text-danger">*</span></label>
                                <input type="text" name="society_name" id="society_name" class="form-control"
                                    placeholder="Society Name">
                                <div id="society_name-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="street_address1">Street Address <span class="text-danger">*</span></label>
                                <textarea name="street_address1" id="street_address1" class="form-control"></textarea>
                                <div id="street_address1-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="street_address2">Street Address 2 </label>
                                <textarea name="street_address2" id="street_address2" class="form-control"></textarea>
                                <div id="street_address2-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="landmark">Landmark <span class="text-danger">*</span></label>
                                <input type="text" name="landmark" id="landmark" class="form-control"
                                    placeholder="Landmark">
                                <div id="landmark-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="pin_code">Pin Code <span class="text-danger">*</span></label>
                                <input type="text" name="pin_code" id="pin_code" class="form-control"
                                    placeholder="Pin Code">
                                <div id="pin_code-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Country <span class="text-danger">*</span></label>
                                <select class=" js-states" name="country_id" id="country-dropdown">
                                    <option value=""></option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->country_id }}">
                                            {{ $country->counrty_name }}</option>
                                    @endforeach
                                </select>
                                <div id="country_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">State <span class="text-danger">*</span></label>
                                <select class=" js-states" name="state_id" id="state-dropdown">
                                    <option value=""></option>
                                </select>
                                <div id="state_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">City <span class="text-danger">*</span></label>
                                <select class=" js-states" name="city_id" id="city-dropdown">
                                    <option value=""></option>
                                </select>
                                <div id="city_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
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
