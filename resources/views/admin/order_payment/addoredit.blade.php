<div class="modal modal-right fade" id="OrderPaymentModel" tabindex="-1" role="dialog" aria-labelledby="OrderPaymentModel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-valide" id="orderpaymentform" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="1">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label" for="amount_paid">Pay Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount_paid" id="amount_paid" class="form-control"
                                    placeholder="Paid Amount">
                                <div id="amount_paid-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Payment Type <span class="text-danger">*</span></label>
                                <select class="single-select-placeholder js-states" name="payment_type" id="payment-type-dropdown">
                                    <option value="1">Offline</option>
                                    <option value="2">Online</option>
                                    <option value="3">Cheque</option>
                                </select>
                                <div id="order_status-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Payment Note </label>
                                <textarea name="payment_note" id="payment_note" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <label class="text-label">Payment Date<span class="text-danger">*</span></label>
                                <input type="date" value="{{ date('Y-m-d') }}" name="payment_date" id="payment_date" class="form-control">
                                <div id="payment_date-error" class="invalid-feedback animated fadeInDown"
                                    style="display: none;"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer modal-footer-fixed px-5">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="subscription_order_id" id="subscription_order_id" value="{{$id}}">
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
