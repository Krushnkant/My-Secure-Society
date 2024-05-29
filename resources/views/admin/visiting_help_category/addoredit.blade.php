<div class="modal modal-right fade" id="CategoryModal" tabindex="-1" role="dialog" aria-labelledby="CategoryModal">
    <div class="modal-dialog modal-ml" role="document">
        <div class="modal-content">
            <form class="form-valide" id="categoryform" action="{{ url('admin/visitinghelpcategory/add') }}" method="post">
            {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Visiting Help Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="1">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label class="text-label">Visiting Help Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="visiting_help_category_name" id="visiting_help_category_name" class="form-control" placeholder="Visiting Help Category Name">
                                <div id="visiting_help_category_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
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
