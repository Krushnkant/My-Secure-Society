@extends('admin.layout.app')
@section('title', 'Designation')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>{{ $designation->designation_name }} Designation Permission</h4>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
      
            <div class="col-lg-12">
                <div class="card">
                   
                    <div class="card-body">
                        <div class="table-responsive">
                            <form class="form-valide" id="permissionForm" action="#" method="post">
                                {{ csrf_field() }}
                            <table class="table table-striped table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Module</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Add</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete</th>
                                        <th class="text-center">Print</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php $i=1; ?>
                                        @foreach($designation_permissions as $designation_permission)
                                        <input type="hidden" value="{{ $designation_permission->company_designation_id }}"
                                            name="company_designation_id">
                                        <input type="hidden" class="eAuthority"
                                            value="{{ $designation_permission->eAuthority }}">
                                        <tr>
                                            <th class="text-center">{{ $i }}</th>
                                            <td>{{ $modules[$i]; }}</td>
                                            <td class="text-center"><input type="checkbox"
                                                    class=" permissionCheckBox"
                                                    value="{{ $designation_permission->can_view }}" name="canViewArr[]" {{
                                                    $designation_permission->can_view==1?"checked":'' }} {{  $designation_permission->can_view==0?"disabled":'' }} ></td>
                                            <td class="text-center"><input type="checkbox"
                                                    class=" permissionCheckBox"
                                                    value="{{ $designation_permission->can_add }}" name="canAddArr[]" {{
                                                    $designation_permission->can_add==1?"checked":'' }} {{  $designation_permission->can_add==0?"disabled":'' }}></td>
                                             <td class="text-center"><input type="checkbox"
                                                class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_edit }}" name="canEditArr[]" {{
                                                $designation_permission->can_edit==1?"checked":'' }} {{  $designation_permission->can_edit==0?"disabled":'' }}></td>        
                                            <td class="text-center"><input type="checkbox"
                                                    class=" permissionCheckBox"
                                                    value="{{ $designation_permission->can_delete }}" name="canDeleteArr[]" {{
                                                    $designation_permission->can_delete==1?"checked":'' }} {{  $designation_permission->can_delete==0?"disabled":'' }}></td>
                                            <td class="text-center"><input type="checkbox"
                                                class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_print }}" name="canPrintArr[]" {{
                                                $designation_permission->can_print==1?"checked":'' }} {{  $designation_permission->can_print==0?"disabled":'' }}></td>        
                                        </tr>
                                        <?php $i++; ?>
                                        @endforeach
                                  </tbody>
                                 
                            </table>
                            @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_edit(2)))
                                <button type="submit" id="savePermissionBtn" class="btn btn-primary">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                            @endif    
                            </form>
                        </div>
                    </div>
                </div>
            </div>
           
        
    </div>

@endsection

@section('js')


    <script  type="text/javascript">
    $('#permissionForm').on('submit', function (e) {
        $("#savePermissionBtn").find('.loadericonfa').show();
        $('#savePermissionBtn').prop('disabled',  true);
        e.preventDefault();

        var permissionArray  = [];
        $(".eAuthority").each(function  () {
            var page_id = $(this).val();
            var can_view = $(this).next().find('input[name="canViewArr[]"]').val();
            var can_add = $(this).next().find('input[name="canAddArr[]"]').val();
            var can_edit = $(this).next().find('input[name="canEditArr[]"]').val();
            var can_delete = $(this).next().find('input[name="canDeleteArr[]"]').val();
            var can_print = $(this).next().find('input[name="canPrintArr[]"]').val();

            var temp =  { page_id: page_id, can_view: can_view, can_add: can_add, can_edit: can_edit, can_edit: can_edit, can_delete: can_delete,can_print: can_print };
            permissionArray.push(temp);
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // console.log(permissionArray);

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.designation.savepermission')  }}",
            data : { "designation_id": $("input[name='company_designation_id']").val(), "permissionData": permissionArray },
            // processData: false,
            // contentType: false,
            success: function (res) {
                 if (res.status == 200) {
                    $("#savePermissionBtn").find('.loadericonfa').hide();
                    $('#savePermissionBtn').prop('disabled', false);
                    toastr.success("Designation Permission Updated", ' Success', { timeOut: 5000 });
                }
            },
            error: function (data) {
                $("#savePermissionBtn").find('.loadericonfa').hide();
                $('#savePermissionBtn').prop ('disabled', false);
                toastr.error("Please try again", 'Error',  { timeOut: 5000 });
            }
        });
    });

    $('.permissionCheckBox').click(function() {
        var thi = $(this);
        if ($(this).is(':checked')) {
            $(thi).attr('checked', true);
            $(thi).val(1);
        } else {
            $(thi).attr('checked', false);
            $(thi).val(2);
        }
   });

    </script>
@endsection