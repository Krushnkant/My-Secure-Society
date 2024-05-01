<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\SocietyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\Rule;

class ResidentController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function resident_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'block_id' => 'required',
            'status' => 'required|in:0,1,5,4',
        ];

        if ($request->has('block_id') && $request->input('block_id') != 0) {
            $rules['block_id'] .= '|exists:society_block,society_block_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $family_members = SocietyMember::with('user')->where('parent_society_member_id',0)->where('society_id',$society_id);
        if($request->status > 0){
            $family_members = $family_members->where('estatus',$request->status);
        }
        if(isset($request->block_id) && $request->block_id > 0 && $request->block_id != null){
            $flat_ids = Flat::where('society_block_id',$request->block_id)->pluck('block_flat_id');
            $family_members = $family_members->whereIn('block_flat_id',$flat_ids);
        }
        $family_members = $family_members->orderBy('created_at', 'DESC')->paginate(10);

        $family_member_arr = array();
        foreach ($family_members as $family_member) {
            $flat_info = getSocietyBlockAndFlatInfo($family_member['block_flat_id']);
            $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['user_id'] = $family_member->user->user_id;
            $temp['full_name'] = $family_member->user->full_name;
            $temp['block_flat_no'] = $flat_info['block_name'] .'-'. $flat_info['flat_no'];
            $temp['profile_pic'] = $family_member->user->profile_pic_url;
            $temp['estatus'] = $family_member->estatus;
            array_push($family_member_arr, $temp);
        }

        $data['resident_list'] = $family_member_arr;
        $data['total_records'] = $family_members->toArray()['total'];
        return $this->sendResponseWithData($data, "All Resident Retrieved Successfully.");
    }

    public function get_resident(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member,society_member_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $family_member = SocietyMember::with('user','flat')->where('society_member_id',$request->society_member_id)->where('society_id',$society_id)->first();
        if (!$family_member) {
            return $this->sendError(404, 'Society member not found.', "Not Found", []);
        }
        $data = array();
            $flat_info = getSocietyBlockAndFlatInfo($family_member['block_flat_id']);
            $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['user_id'] = $family_member->user->user_id;
            $temp['full_name'] = $family_member->user->full_name;
            $temp['block_flat_no'] = $flat_info['block_name'] .'-'. $flat_info['flat_no'];
            $temp['profile_pic'] = $family_member->user->profile_pic_url;
            $temp['gender'] = $family_member->user->gender;
            $temp['mobile_no'] = $family_member->user->mobile_no;
            $temp['email'] = $family_member->user->email;
            $temp['society_designation_id'] = $family_member->resident_designation_id;
            $temp['society_department_id'] = $family_member->society_department_id;
            $temp['estatus'] = $family_member->estatus;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Resident Detail Retrieved Successfully.");
    }

    public function change_status(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member,society_member_id,deleted_at,Null',
            'status' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Retrieve the current status
                    $currentStatus = SocietyMember::where('society_member_id', $request->input('society_member_id'))->value('estatus');
                    
                    // Validate the status transition
                    $validTransitions = $this->validateStatusTransition($currentStatus, $value);
                    if (!$validTransitions) {
                        $fail("Invalid status transition from $currentStatus to $value.");
                    }
                },
            ],
        ]);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Update the status
        $family_member = SocietyMember::where('society_member_id', $request->society_member_id)->firstOrFail();
        $family_member->estatus = $request->status;
        $family_member->save();
        if($request->status == 3){
            $family_member->delete();
        }

        return $this->sendResponseSuccess("Status Updated Successfully.");
    }

    private function validateStatusTransition($currentStatus, $newStatus)
    {
        switch ($currentStatus) {
            case 4: // Pending
                return in_array($newStatus, [1, 5]); // Allowed transitions to Active or Rejected
            case 1: // Active
                return in_array($newStatus, [2, 3]); // Allowed transitions to Inactive or Delete
            case 2: // Inactive
                return $newStatus == 3; // Allowed transition to Delete
            case 3: // Delete
            case 5: // Rejected
                return false; // No allowed transitions from Delete or Rejected
            default:
                return false; // Invalid current status
        }
    }
}
