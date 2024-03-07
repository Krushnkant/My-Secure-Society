<div class="modal modal-right fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="UserModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form class="form-valide" id="userform" action="#" method="post">
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
                                <label class="text-label">Full Name *</label>
                                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Full Name">
                                <div id="full_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">1 - Company Admin User, 2 - Resident App User, 3 - Guard App User, 4 - App User, 5 - Daily Help User, 6 - Staff Member
                            <div class="form-group">
                                <label class="text-label">User Type *</label>
                                <select class="single-select-placeholder js-states">
                                    <option value="1">Super Admin</option>
                                    <option value="Hawaii">Hawaii</option>
                                    <option value="California">California</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Email Address*</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="inputGroupPrepend2" aria-describedby="inputGroupPrepend2" placeholder="example@example.com.com" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Phone Number*</label>
                                <div class="input-group">
                                    <input type="text" name="phoneNumber" class="form-control" placeholder="(+1)408-657-9007" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Password*</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" id="inputGroupPrepend2" aria-describedby="inputGroupPrepend2" placeholder="example@example.com.com" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                            <label class="text-label">Password*</label>
                                <div class="input-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="1" checked> Male</label>
                                        
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" value="2"> Female</label>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Blood Group *</label>
                                <select class="single-select-placeholder js-states">
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
