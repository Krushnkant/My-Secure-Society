<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StaffDutyAreaTime;
use App\Models\StaffDutyAttendance;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

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
            'designation_id' => 'required|exists:resident_designation,resident_designation_id,deleted_at,NULL,use_for,2,society_id,'.$society_id,
            'full_name' => 'required|string|max:50',
            'gender' => ['required', Rule::in([1, 2])],
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
            'password' => 'required|string|max:10',
        ];

        if ($request->has('staff_member_id') && $request->input('staff_member_id') != 0) {
            $rules['staff_member_id'] .= '|exists:society_staff_member,society_staff_member_id,deleted_at,NULL,society_id,'.$society_id;
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
                $staff->resident_designation_id = $request->designation_id;
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
                    $staff->resident_designation_id = $request->designation_id;
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

        if ($request->has('department_id') && $request->input('department_id') != 0) {
            $rules['department_id'] .= '|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Retrieve service providers based on parameters
        $query = StaffMember::with(['areatime.duty_area', 'user','designation'])->where('estatus', 1);

        if ($request->has('department_id') && $request->input('department_id') != 0) {
            $query->where('society_department_id', $request->department_id);
        }

        $perPage = 10;
        $staffs = $query->paginate($perPage);

        $staff_arr = [];
        foreach ($staffs as $staff) {
            $temp['staff_member_id'] = $staff->society_staff_member_id;
            $temp['stand_area_id'] =  $staff->areatime->staff_duty_area_id;
            $temp['stand_area_name'] = $staff->areatime->duty_area->area_name;
            $temp['designation_id'] =  $staff->resident_designation_id;
            $temp['designation_name'] = $staff->designation->designation_name ?? "";
            $temp['duty_start_time'] = $staff->duty_start_time;
            $temp['duty_end_time'] = $staff->duty_end_time;
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
        $temp['designation_id'] =  $staff->resident_designation_id;
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


    public function save_staff_member_duty_area(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $rules = [
            'staff_member_id' => 'required|integer|exists:society_staff_member,society_staff_member_id,deleted_at,NULL',
            // 'stand_area_id' => 'required|integer|integer|exists:staff_duty_area,staff_duty_area_id,deleted_at,NULL,society_id,'.$society_id,
            'duty_start_time' => 'required|date_format:H:i',
            'duty_end_time' => 'required|date_format:H:i|after:duty_start_time',
            'duty_area' => 'required|array|min:1',
            'duty_area.*.duty_area_time_id' => 'required|integer',
            'duty_area.*.staff_duty_area_id' => 'required|integer|exists:staff_duty_area,staff_duty_area_id,deleted_at,NULL,society_id,'.$society_id,
            'duty_area.*.visit_time' => 'required|date_format:H:i',
            'duty_area.*.is_standing_location' => 'required|in:1,2',
            'duty_area.*.is_removed' => 'required|in:1,2',
        ];

        if ($request->has('duty_area.*.duty_area_time_id') && $request->input('duty_area.*.duty_area_time_id') != 0) {
            $rules['duty_area.*.duty_area_time_id'] .= '|exists:staff_duty_area_time,duty_area_time_id,deleted_at,NULL';
        }

        $messages = [
            'duty_area.*.duty_area_time_id.required' => 'The duty area time ID field is required.',
            'duty_area.*.duty_area_time_id.exists' => 'The selected duty area time ID is invalid.',
            'duty_area.*.staff_duty_area_id.required' => 'The staff duty area ID field is required.',
            'duty_area.*.staff_duty_area_id.exists' => 'The selected staff duty area ID is invalid.',
            'duty_area.*.visit_time.required' => 'The visit time field is required.',
            'duty_area.*.is_standing_location.required' => 'The standing location field is required.',
            'duty_area.*.is_removed.required' => 'The duty area is removed field is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Custom validation logic to ensure exactly one object has is_standing_location set to 1
        $standingLocations = array_filter($request->duty_area, function ($duty) {
            return $duty['is_standing_location'] == 1;
        });

        if (count($standingLocations) !== 1) {
            return $this->sendError(422, ['duty_area' => ['Exactly one duty area must have is_standing_location set to 1']], "Validation Errors", []);
        }

        DB::beginTransaction();
        try {

            $staff = StaffMember::find($request->staff_member_id);
            $staff->duty_start_time = $request->duty_start_time;
            $staff->duty_end_time = $request->duty_end_time;
            $staff->save();

            foreach ($request->duty_area as $area) {
                if ($area['is_removed'] == 2) {
                    if (isset($area['duty_area_time_id']) && $area['duty_area_time_id'] != "" && $area['duty_area_time_id'] != 0) {
                        $areaTime = StaffDutyAreaTime::find($area['duty_area_time_id']);
                        $areaTime->staff_duty_area_id = $area['staff_duty_area_id'];
                        $areaTime->is_standing_location = $area['is_standing_location'];
                        $areaTime->visit_time = $area['visit_time'];
                        $areaTime->created_by = Auth::user()->user_id;
                        $areaTime->updated_by = Auth::user()->user_id;
                        $areaTime->save();
                    }else{
                        $areaTime = new StaffDutyAreaTime();
                        $areaTime->society_staff_member_id = $staff->society_staff_member_id;
                        $areaTime->staff_duty_area_id = $area['staff_duty_area_id'];
                        $areaTime->is_standing_location = $area['is_standing_location'];
                        $areaTime->visit_time = $area['visit_time'];
                        $areaTime->created_by = Auth::user()->user_id;
                        $areaTime->updated_by = Auth::user()->user_id;
                        $areaTime->save();

                    }
                } elseif ($area['is_removed'] == 1 && isset($area['duty_area_time_id']) && $area['duty_area_time_id'] != "" && $area['duty_area_time_id'] != 0) {
                    // Handle deleting existing slots if needed
                    StaffDutyAreaTime::destroy($area['duty_area_time_id']);
                }
            }

        DB::commit();

        $data = array();
        $temp['staff_member_id'] = $staff->society_staff_member_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Staff Duty Area successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, $e, "Internal Server Error", []);
        }
    }

    public function staff_member_duty_area_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'staff_member_id' => 'required|exists:society_staff_member,society_staff_member_id,deleted_at,NULL',
        ];


        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Retrieve service providers based on parameters
        $query = StaffDutyAreaTime::with('duty_area')->where('estatus', 1)->where('society_staff_member_id', $request->staff_member_id);

        $perPage = 10;
        $times = $query->paginate($perPage);

        $time_arr = [];
        foreach ($times as $time) {
            $temp['duty_area_time_id'] = $time->duty_area_time_id;
            $temp['staff_duty_area_id'] = $time->staff_duty_area_id;
            $temp['duty_area_name'] = $time->duty_area->area_name;
            $temp['visit_time'] = $time->visit_time;
            $temp['is_standing_location'] = $time->is_standing_location;
            array_push($time_arr, $temp);
        }

        $data['duty_area_list'] = $time_arr;
        $data['total_records'] = $times->total();

        return $this->sendResponseWithData($data, "All Staff Duty Area Successfully.");
    }

    public function get_staff_member_duty_area(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duty_area_time_id' => 'required|exists:staff_duty_area_time,duty_area_time_id,deleted_at,NULL',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = StaffDutyAreaTime::find($request->duty_area_time_id);

        if (!$staff) {
            return response()->json(['error' => 'duty area not found'], 404);
        }

        $data = array();
        $temp['duty_area_time_id'] = $staff->duty_area_time_id;
        $temp['staff_duty_area_id'] = $staff->staff_duty_area_id;
        $temp['visit_time'] = $staff->visit_time;

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All staff duty area Retrieved Successfully.");
    }

    public function delete_staff_member_duty_area(Request $request)
    {
        $designation_id = $this->payload['designation_id'];
        if($designation_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'duty_area_time_id' => 'required|exists:staff_duty_area_time,duty_area_time_id,deleted_at,NULL',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = StaffDutyAreaTime::find($request->duty_area_time_id);
        if(getResidentDesignation($designation_id) == "Society Member" &&  $staff->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
        $staff->estatus = 3;
        $staff->save();
        $staff->delete();

        return $this->sendResponseSuccess("staff duty area deleted successfully.");
    }

    public function staff_member_fill_attendance(Request $request)
    {
        $rules = [
            'duty_area_time_id' => 'required|exists:staff_duty_area_time,duty_area_time_id,deleted_at,NULL',
            'attendance_photo' => 'required|image|mimes:jpeg,png,jpg',
            'attendance_status' => 'required|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        DB::beginTransaction();
        try {
            $area_time = StaffDutyAreaTime::find($request->duty_area_time_id);
            $attendance = new StaffDutyAttendance();
            $attendance->duty_area_time_id = $request->duty_area_time_id;
            $attendance->society_staff_member_id = $area_time->society_staff_member_id;
            $attendance->attendance_status = $request->attendance_status;
            $image_full_path = "";
            if ($request->hasFile('attendance_photo')) {
                $image = $request->file('attendance_photo');
                $image_full_path = UploadImage($image,'images/attendance_photo');
            }
            $attendance->selfie_photo =  $image_full_path;
            $attendance->updated_by = Auth::user()->user_id;
            $attendance->save();

        DB::commit();

        $data = array();
        $attendance = StaffDutyAttendance::with('duty_area_time.duty_area')->where('staff_duty_attendance_id',$attendance->staff_duty_attendance_id)->first();
        $temp['staff_duty_attendance_id'] = $attendance->staff_duty_attendance_id;
        $temp['duty_area_name'] = $attendance->duty_area_time->duty_area->area_name;
        $temp['attendance_status'] = $attendance->attendance_status;
        $temp['attendance_photo'] = url($attendance->selfie_photo);
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'attendance  successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while saving the attendance', "Internal Server Error", []);
        }
    }

    public function staff_member_attendance_list(Request $request)
    {
        $rules = [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'attendance_status' => 'required|integer|in:0,1,2,3'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }


        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $attendance_status = $request->attendance_status;

        // Build the query
        $query = StaffDutyAttendance::with(['duty_area_time.duty_area'])
        ->whereBetween('updated_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);

        if ($attendance_status != 0) {
            $query->where('attendance_status', $attendance_status);
        }
        $perPage = 10;
        $attendances = $query->paginate($perPage);

        $attendance_list = [];
        foreach ($attendances as $attendance) {
            $temp['staff_duty_attendance_id'] = $attendance->staff_duty_attendance_id;
            $temp['duty_area_name'] = $attendance->duty_area_time->duty_area->area_name;
            $temp['attendance_status'] = $attendance->attendance_status;
            $temp['attendance_photo'] = url($attendance->selfie_photo);
            $updated_at = Carbon::parse($attendance->updated_at);
            $temp['date'] = $updated_at->format('d-m-Y');
            array_push($attendance_list, $temp);
        }

        $data['total_records'] = $attendances->total();
        $data['attendance_list'] = $attendance_list;

        return $this->sendResponseWithData($data, "Attendance list retrieved successfully.");
    }
}
