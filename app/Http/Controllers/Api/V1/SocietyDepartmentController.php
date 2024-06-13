<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyDepartment;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SocietyDepartmentController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_department(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'department_id' => 'required|numeric',
            'department_name' => [
                'required',
                'max:50',
                Rule::unique('society_department', 'department_name')
                    ->where(function ($query) use ($society_id) {
                        return $query->where('society_id', $society_id)
                                     ->whereNull('deleted_at');
                    })
                    ->ignore($request->department_id, 'society_department_id'),
            ],
            'society_id' => 'required|exists:society',
        ];
        if ($request->has('department_id') && $request->input('department_id') != 0) {
            $rules['department_id'] .= '|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->department_id == 0){
            $department = New SocietyDepartment();
            $department->society_id = $society_id;
            $department->created_at = now();
            $department->created_by = Auth::user()->user_id;
            $department->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $department = SocietyDepartment::find($request->department_id);
            if($request->calling_by == 1 &&  $department->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
            $department->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $department->department_name = $request->department_name;
        $department->save();

        $data = array();
        $temp['department_id'] = $department->society_department_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Department ".$action." Successfully");
    }

    public function department_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $departments = SocietyDepartment::with('society_staff_members')->where('society_id', $society_id)->where('estatus', 1)->orderBy('department_name', 'ASC')->paginate(10);
        $department_arr = array();
        foreach ($departments as $department) {
            $temp['department_id'] = $department['society_department_id'];
            $temp['department_name'] = $department->department_name;
            $temp['total_member'] = count($department->society_staff_members);
            array_push($department_arr, $temp);
        }

        $data['department_list'] = $department_arr;
        $data['total_records'] = $departments->toArray()['total'];
        return $this->sendResponseWithData($data, "All Department Successfully.");
    }

    public function delete_department(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $department = SocietyDepartment::find($request->department_id);
        if($request->calling_by == 1 &&  $department->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        $isReferenced = StaffMember::where('society_department_id', $department->society_department_id)->exists();
        if ($isReferenced) {
            return $this->sendError(400, 'Department cannot be deleted because it is referenced in other records.', "Bad Request", []);
        }

        if ($department) {
            $department->estatus = 3;
            $department->save();
            $department->delete();
        }
        return $this->sendResponseSuccess("department deleted Successfully.");
    }
}
