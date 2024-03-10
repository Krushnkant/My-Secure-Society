<div class="modal modal-right fade" id="OrderModal" tabindex="-1" role="dialog" aria-labelledby="OrderModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-valide" id="orderform" method="post">
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
                                <label class="text-label">Society <span class="text-danger">*</span></label>
                                <select class=" js-states" name="society_id " id="society-dropdown">
                                    <option value=""></option>
                                    @foreach ($societies as $society)
                                        <option value="{{ $society->society_id }}">
                                            {{ $society->society_name }}</option>
                                    @endforeach
                                </select>
                                <div id="society_id-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="total_flat">Total Flat <span class="text-danger">*</span></label>
                                <input type="number" name="total_flat" id="total_flat" class="form-control"
                                    placeholder="Total Flat">
                                <div id="total_flat-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="amount_per_flat">Amount Per Flat <span class="text-danger">*</span></label>
                                <input type="number" name="amount_per_flat" id="amount_per_flat" class="form-control"
                                    placeholder="Amount Per Flat">
                                <div id="amount_per_flat-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="sub_total_amount">Sub Total Amount <span class="text-danger">*</span></label>
                                <input type="number" name="sub_total_amount" id="sub_total_amount" class="form-control"
                                    placeholder="Sub Total Amount">
                                <div id="sub_total_amount-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="gst_percent">GST Percent<span class="text-danger">*</span></label>
                                <input type="number" name="gst_percent" id="gst_percent" class="form-control"
                                    placeholder="GST Percent">
                                <div id="gst_percent-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="gst_amount">GST Amount<span class="text-danger">*</span></label>
                                <input type="number" name="gst_amount" id="gst_amount" class="form-control"
                                    placeholder="GST Amount">
                                <div id="gst_amount-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="total_amount">Total Amount<span class="text-danger">*</span></label>
                                <input type="number" name="total_amount" id="total_amount" class="form-control"
                                    placeholder="Total Amount">
                                <div id="total_amount-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="total_paid_amount">Total Paid Amount<span class="text-danger">*</span></label>
                                <input type="number" name="total_paid_amount" id="total_paid_amount" class="form-control"
                                    placeholder="Total Paid Amount">
                                <div id="total_paid_amount-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="total_outstanding_amount">Total Outstanding Amount<span class="text-danger">*</span></label>
                                <input type="number" name="total_outstanding_amount" id="total_outstanding_amount" class="form-control"
                                    placeholder="Total Outstanding Amount">
                                <div id="total_outstanding_amount-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Order Status <span class="text-danger">*</span></label>
                                <select class=" js-states" name="order_status" id="order-status-dropdown">
                                    <option value=""></option>
                                </select>
                                <div id="order_status-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                                <div id="due_date-error" class="invalid-feedback animated fadeInDown"
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
