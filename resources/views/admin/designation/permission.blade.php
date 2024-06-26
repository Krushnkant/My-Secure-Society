@extends('admin.layout.app')
@section('title', 'Designation')

@section('pageTitleAndBreadcrumb')
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Permission for {{ $designation->designation_name }} </h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.designation.list') }}">Designation</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $designation->designation_name }}</a></li>
        </ol>
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
                        <table id="permissionTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Module</th>
                                    <th class="text-center"> View <input type="checkbox" id="selectViewAllCheckbox"></th>
                                    <th class="text-center"> Add <input type="checkbox" id="selectAddAllCheckbox"></th>
                                    <th class="text-center"> Edit <input type="checkbox" id="selectEditAllCheckbox"></th>
                                    <th class="text-center"> Delete <input type="checkbox" id="selectDeleteAllCheckbox"></th>
                                    <th class="text-center"> Print <input type="checkbox" id="selectPrintAllCheckbox"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($designation_permissions as $designation_permission)
                                    
                                    <tr>
                                        <input type="hidden" value="{{ $designation_permission->company_designation_id }}"
                                        name="company_designation_id">
                                    <input type="hidden" class="eAuthority"
                                        value="{{ $designation_permission->eAuthority }}">
                                        <th class="text-center">{{ $i }}</th>
                                        <td>{{ $modules[$i] }}</td>
                                        <td class="text-center"><input type="checkbox" class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_view }}" name="canViewArr[]"
                                                {{ $designation_permission->can_view == 1 ? 'checked' : '' }}
                                                {{ $designation_permission->can_view == 0 ? 'disabled' : '' }}></td>
                                        <td class="text-center"><input type="checkbox" class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_add }}" name="canAddArr[]"
                                                {{ $designation_permission->can_add == 1 ? 'checked' : '' }}
                                                {{ $designation_permission->can_add == 0 ? 'disabled' : '' }}></td>
                                        <td class="text-center"><input type="checkbox" class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_edit }}" name="canEditArr[]"
                                                {{ $designation_permission->can_edit == 1 ? 'checked' : '' }}
                                                {{ $designation_permission->can_edit == 0 ? 'disabled' : '' }}></td>
                                        <td class="text-center"><input type="checkbox" class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_delete }}" name="canDeleteArr[]"
                                                {{ $designation_permission->can_delete == 1 ? 'checked' : '' }}
                                                {{ $designation_permission->can_delete == 0 ? 'disabled' : '' }}></td>
                                        <td class="text-center"><input type="checkbox" class=" permissionCheckBox"
                                                value="{{ $designation_permission->can_print }}" name="canPrintArr[]"
                                                {{ $designation_permission->can_print == 1 ? 'checked' : '' }}
                                                {{ $designation_permission->can_print == 0 ? 'disabled' : '' }}></td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                            
                        </table>
                        @if (getUserDesignationId() == 1 || (getUserDesignationId() != 1 && is_edit(2)))
                            <div class="text-right mt-5 btn-page">
                                <button type="submit" id="savePermissionBtn" class="btn btn-primary">Save <i
                                        class="fa fa-circle-o-notch fa-spin loadericonfa"
                                        style="display:none;"></i></button>
                            </div>
                        @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection

@section('js')
     <!-- Datatable -->
     <script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
     <script src="{{ asset('/js/plugins-init/datatables.init.js') }}"></script>

    <script type="text/javascript">
        $('#permissionForm').on('submit', function(e) {
            $("#savePermissionBtn").find('.loadericonfa').show();
            $('#savePermissionBtn').prop('disabled', true);
            e.preventDefault();

            var permissionArray = [];
            $(".eAuthority").each(function() {
                // var page_id = $(this).val();
                // var can_view = $(this).next().find('input[name="canViewArr[]"]').val();
                // var can_add = $(this).next().find('input[name="canAddArr[]"]').val();
                // var can_edit = $(this).next().find('input[name="canEditArr[]"]').val();
                // var can_delete = $(this).next().find('input[name="canDeleteArr[]"]').val();
                // var can_print = $(this).next().find('input[name="canPrintArr[]"]').val();

                var page_id = $(this).val();
                var row = $(this).closest('tr');
                var can_view = row.find('input[name="canViewArr[]"]').val();
                var can_add = row.find('input[name="canAddArr[]"]').val();
                var can_edit = row.find('input[name="canEditArr[]"]').val();
                var can_delete = row.find('input[name="canDeleteArr[]"]').val();
                var can_print = row.find('input[name="canPrintArr[]"]').val();

                var temp = {
                    page_id: page_id,
                    can_view: can_view,
                    can_add: can_add,
                    can_edit: can_edit,
                    can_edit: can_edit,
                    can_delete: can_delete,
                    can_print: can_print
                };
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
                url: "{{ route('admin.designation.savepermission') }}",
                data: {
                    "designation_id": $("input[name='company_designation_id']").val(),
                    "permissionData": permissionArray
                },
                // processData: false,
                // contentType: false,
                success: function(res) {
                    if (res.status == 200) {
                        $("#savePermissionBtn").find('.loadericonfa').hide();
                        $('#savePermissionBtn').prop('disabled', false);
                        toastr.success("Designation Permission Updated", ' Success', {
                            timeOut: 5000
                        });
                    }
                },
                error: function(data) {
                    $("#savePermissionBtn").find('.loadericonfa').hide();
                    $('#savePermissionBtn').prop('disabled', false);
                    toastr.error("Please try again", 'Error', {
                        timeOut: 5000
                    });
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

        $('.permissionCheckBox[name="canEditArr[]"]').click(function() {
            var $editCheckbox = $(this);
            var $viewCheckbox = $editCheckbox.closest('tr').find('.permissionCheckBox[name="canViewArr[]"]');

            if ($editCheckbox.is(':checked')) {
                $viewCheckbox.prop('checked', true).val(1);
            }
            // else {
            //     $viewCheckbox.prop('checked', false).val(0);
            // }
        });

        $(document).ready(function() {
    // Event handler for "View" column checkbox
    $('#selectViewAllCheckbox').click(function() {
        var isChecked = $(this).prop('checked');
        $('.permissionCheckBox[name="canViewArr[]"]').each(function() {
            if (!$(this).is(':disabled')) {
                $(this).prop('checked', isChecked).val(isChecked ? 1 : 0);
            }
        });
    });

    // Event handler for "Add" column checkbox
    $('#selectAddAllCheckbox').click(function() {
        var isChecked = $(this).prop('checked');
        $('.permissionCheckBox[name="canAddArr[]"]').each(function() {
            if (!$(this).is(':disabled')) {
                $(this).prop('checked', isChecked).val(isChecked ? 1 : 0);
            }
        });
    });

    // Event handler for "Edit" column checkbox
    $('#selectEditAllCheckbox').click(function() {
        var isChecked = $(this).prop('checked');
        $('.permissionCheckBox[name="canEditArr[]"]').each(function() {
            if (!$(this).is(':disabled')) {
                $(this).prop('checked', isChecked).val(isChecked ? 1 : 0);
            }
        });
    });

    // Event handler for "Delete" column checkbox
    $('#selectDeleteAllCheckbox').click(function() {
        var isChecked = $(this).prop('checked');
        $('.permissionCheckBox[name="canDeleteArr[]"]').each(function() {
            if (!$(this).is(':disabled')) {
                $(this).prop('checked', isChecked).val(isChecked ? 1 : 0);
            }
        });
    });

    // Event handler for "Print" column checkbox
    $('#selectPrintAllCheckbox').click(function() {
        var isChecked = $(this).prop('checked');
        $('.permissionCheckBox[name="canPrintArr[]"]').each(function() {
            if (!$(this).is(':disabled')) {
                $(this).prop('checked', isChecked).val(isChecked ? 1 : 0);
            }
        });
    });
});

$(document).ready(function() {
    // Check if DataTables is already initialized on the table
    if (!$.fn.DataTable.isDataTable('#permissionTable')) {
        $('#permissionTable').DataTable({
            "paging": false, // Disable pagination
            "searching": false, // Disable search
            "info": false, // Disable info text
            "ordering": false // Disable ordering
            
        });
        
    }
});
    </script>
@endsection
