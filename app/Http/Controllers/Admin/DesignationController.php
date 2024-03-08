<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers;
use App\Models\CompanyDesignationAuthority;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    
    public function index()
    {
        return view('admin.designation.list');
    }
    
    public function listdata(Request $request){
      
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = Designation::select('*')->where('company_designation_id','<>',1);
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('designation_name', 'like', "%".$search."%");
        });

        $orderByName = 'designation_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'designation_name';
                break;  
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'designation_name.required' =>'Please provide a designation name',
        ];

        $validator = Validator::make($request->all(), [
            'designation_name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $designation = new Designation();
            $designation->designation_name = $request->designation_name;
            $designation->created_by = Auth::user()->user_id;
            $designation->updated_by = Auth::user()->user_id;
            $designation->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $designation->save();

            $this->defalt_permission($designation->company_designation_id);
            
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $designation = Designation::find($request->id);
            if ($designation) {
                $designation->designation_name = $request->designation_name;
                $designation->updated_by = Auth::user()->user_id;
                $designation->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    protected function defalt_permission($id){
        $modules = Helpers::getModulesArray();

        foreach ($modules as $key => $module) {
            $user_permission = new CompanyDesignationAuthority();
            $user_permission->company_designation_id = $id;
            $user_permission->eAuthority = $key;
            $user_permission->updated_by  = Auth::user()->user_id;
            
            $user_permission->can_view = 2;
            $user_permission->can_add = 2;
            $user_permission->can_edit = 2;
            $user_permission->can_delete = 2;
            $user_permission->can_print = 2;
            if($key == 12){
                $user_permission->can_add = 0;
                $user_permission->can_delete = 0;
            }
            if($key == 2){
                $user_permission->can_add = 0;
            }
            if($key == 11){
                $user_permission->can_add = 0;
            }
            $user_permission->save();
        }
    }

    public function edit($id){
        $designation = Designation::find($id);
        return response()->json($designation);
    }

    public function delete($id){
        $designation = Designation::find($id);
        if ($designation){
            $designation->estatus = 3;
            $designation->save();
            $designation->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $designation = Designation::find($id);
        if ($designation->estatus==1){
            $designation->estatus = 2;
            $designation->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($designation->estatus==2){
            $designation->estatus = 1;
            $designation->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        Designation::whereIn('company_designation_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }


    public function permissiondesignation($id)
    {
        $modules = Helpers::getModulesArray();
        $designation = Designation::select('designation_name')->find($id);
        $designation_permissions = CompanyDesignationAuthority::where('company_designation_id', $id)->orderBy('eAuthority', 'asc')->get();
       // $user_permissions = CompanyDesignationAuthority::join('project_pages', 'user_permissions.project_page_id', '=', 'project_pages.id')->select('user_permissions.*', 'project_pages.label')->where('user_permissions.user_id', $id)->orderBy('user_permissions.project_page_id', 'asc')->get();
        return view('admin.designation.permission', compact('designation_permissions','modules','designation'));
    }

    public function savepermission(Request $request)
    {
        foreach ($request->permissionData as $pdata) {
            $designation_permission = CompanyDesignationAuthority::where('company_designation_id', $request->designation_id)->where('eAuthority', $pdata['page_id'])->first();
            if($designation_permission){
                $designation_permission->can_view = $pdata['can_view'];
                $designation_permission->can_add = $pdata['can_add'];
                $designation_permission->can_edit = $pdata['can_edit'];
                $designation_permission->can_delete = $pdata['can_delete'];
                $designation_permission->can_print = $pdata['can_print'];
            }else{
                $designation_permission = New CompanyDesignationAuthority();
                $designation_permission->company_designation_id = $request->designation_id;
                $designation_permission->eAuthority = $pdata['page_id'];
                $designation_permission->can_view = $pdata['can_view'];
                $designation_permission->can_add = $pdata['can_add'];
                $designation_permission->can_edit = $pdata['can_edit'];
                $designation_permission->can_delete = $pdata['can_delete'];
                $designation_permission->can_print = $pdata['can_print'];
            }
            $designation_permission->updated_by  = Auth::user()->user_id;
            $designation_permission->save();
           
        }

        return response()->json(['status' => '200']);
    }

}
