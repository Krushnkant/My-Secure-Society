<div class="modal modal-right fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="UserModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form class="form-valide" id="userform" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
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
                                <label class="text-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Email Address">
                                    <div id="email-error" class="invalid-feedback animated fadeInDown"
                                        style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Designation <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="designation">
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->company_designation_id }}">
                                            {{ $designation->designation_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">User Type <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="user_type">
                                    <option value="1">Company Admin User</option>
                                    <option value="2">Resident App User</option>
                                    <option value="3">Guard App User</option>
                                    <option value="4">App User</option>
                                    <option value="5">Daily Help User</option>
                                    <option value="6">Staff Member</option>
                                </select>
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
                                <label class="text-label">Blood Group <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="blood_group">
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
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
                            <div class="form-group ">
                                <label class="col-form-label" for="profilePic">Profile Image
                                </label>
                                <input type="file" class="form-control-file" id="profile_pic" onchange=""
                                    name="profile_pic">
                                <div id="profilepic-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                                <img src="{{ asset('image/avtar.png') }}" class=""
                                    id="profilepic_image_show" height="100px" width="100px"
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
