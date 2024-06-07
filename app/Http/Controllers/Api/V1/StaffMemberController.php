<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffMemberController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_staff_member(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $rules = [
            'staff_member_id' => 'required',
            'department_id' => 'required|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id,
            'full_name' => 'required|string|max:50',
            'gender' => ['required', Rule::in([1, 2])],
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
            'password' => 'required|string|max:10',
        ];

        if ($request->has('staff_member_id') && $request->input('staff_member_id') != 0) {
            $rules['staff_member_id'] .= '|exists:society_staff_member,society_staff_member_id,deleted_at,NULL';
        }

        if ($request->staff_member_id > 0) {
            $staff = StaffMember::find($request->staff_member_id);
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->where('user_type',6)->ignore($staff->user_id,'user_id')->whereNull('deleted_at'),
            ];
        } else {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->where('user_type',6)->whereNull('deleted_at'),
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        DB::beginTransaction();
        try {

        if($request->staff_member_id == 0){
            $user = new User();
            $user->user_code = rand(100000, 999999);
            $user->full_name = $request->full_name;
            $user->mobile_no = $request->mobile_no;
            $user->password = Hash::make($request->password);
            $user->user_type = 6;
            $user->gender = $request->gender;
            $image_full_path = "";
            if ($request->hasFile('profile_pic')) {
                $image = $request->file('profile_pic');
                $image_full_path = UploadImage($image,'images/profile_pic');
            }
            $user->profile_pic_url =  $image_full_path;
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $user->save();

            if($user){
                $staff = new StaffMember();
                $staff->user_id = $user->user_id;
                $staff->society_department_id = $request->department_id;
                $staff->created_by = Auth::user()->user_id;
                $staff->updated_by = Auth::user()->user_id;
                $staff->save();
            }
        }else{
                $user = User::find($staff->user_id);
                $designation_id = $this->payload['designation_id'];
                if(getResidentDesignation($designation_id) == "Society Member" &&  $user->created_by != auth()->id()){
                    return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
                }

                $user->full_name = $request->full_name;
                $user->mobile_no = $request->mobile_no;
                $user->gender = $request->gender;
                $user->password = $request->password;
                if(isset($user->profile_pic)) {
                    $old_image = public_path('images/profile_pic/' . $user->profile_pic);
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                $image_full_path = "";
                if ($request->hasFile('profile_pic')) {
                    $image = $request->file('profile_pic');
                    $image_full_path = UploadImage($image,'images/profile_pic');
                }
                $user->profile_pic_url =  $image_full_path;
                $user->updated_by = Auth::user()->user_id;
                $user->save();
                if($staff){
                    $staff->society_department_id = $request->department_id;
                    $staff->updated_by = Auth::user()->user_id;
                    $staff->save();
                }
        }

        DB::commit();

        $data = array();
        $temp['staff_member_id'] = $staff->society_staff_member_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Staff Member successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while saving the staff member.', "Internal Server Error", []);
        }
    }


    public function staff_member_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'department_id' => 'required',
        ];

        if ($request->has('staff_member_id') && $request->input('staff_member_id') != 0) {
            $rules['department_id'] .= '|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Retrieve service providers based on parameters
        $query = StaffMember::with(['user'])->where('estatus', 1);

        if ($request->has('department_id') && $request->input('department_id') != 0) {
            $query->where('society_department_id', $request->department_id);
        }

        $perPage = 10;
        $staffs = $query->paginate($perPage);

        $staff_arr = [];
        foreach ($staffs as $staff) {
            $temp['staff_member_id'] = $staff->society_staff_member_id;
            $temp['stand_area_id'] = 0;
            $temp['stand_area_name'] = "";
            $temp['duty_start_time'] = "";
            $temp['duty_end_time'] = "";
            $temp['full_name'] = $staff->user->full_name ?? "";
            $temp['profile_pic'] = $staff->user->profile_pic_url ? url($staff->user->profile_pic_url) : "";
            array_push($staff_arr, $temp);
        }

        $data['member_list'] = $staff_arr;
        $data['total_records'] = $staffs->total();

        return $this->sendResponseWithData($data, "All Staff Member Successfully.");
    }

    public function get_staff_member(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_member_id' => 'required|exists:society_staff_member,society_staff_member_id,deleted_at,NULL',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = StaffMember::with('user')->find($request->staff_member_id);

        if (!$staff) {
            return response()->json(['error' => 'member not found'], 404);
        }

        $data = array();
        $temp['staff_member_id'] = $staff->society_staff_member_id;
        $temp['user_id'] = $staff->user_id;
        $temp['daily_help_user_passcode'] = isset($staff->user)?$staff->user->user_code:"";
        $temp['full_name'] = isset($staff->user)?$staff->user->full_name:"";
        $temp['mobile_no'] = isset($staff->user)?$staff->user->mobile_no:"";
        $temp['profile_pic'] = isset($staff->user) && $staff->user->profile_pic_url != ""?url($staff->user->profile_pic_url):"";
        $temp['gender'] = isset($staff->user)?$staff->user->gender:"";

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All staff member Retrieved Successfully.");
    }

    public function delete_staff_member(Request $request)
    {
        $designation_id = $this->payload['designation_id'];
        if($designation_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'staff_member_id' => 'required|exists:society_staff_member,society_staff_member_id,deleted_at,NULL',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $staff = StaffMember::find($request->staff_member_id);
        if(getResidentDesignation($designation_id) == "Society Member" &&  $staff->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
        $staff->estatus = 3;
        $staff->save();
        $staff->delete();

        return $this->sendResponseSuccess("staff member deleted successfully.");
    }
}
