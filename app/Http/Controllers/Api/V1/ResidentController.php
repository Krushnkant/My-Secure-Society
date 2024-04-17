<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
        $family_members = SocietyMember::with('user','flat')->where('society_id',$society_id);
        if($request->status > 0){
            $family_members = $family_members->where('estatus',$request->status);
        }
        $family_members = $family_members->orderBy('created_at', 'DESC')->paginate(10);

        $family_member_arr = array();
        foreach ($family_members as $family_member) {
            $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['user_id'] = $family_member->user->user_id;
            $temp['full_name'] = $family_member->user->full_name;
            $temp['flat_no'] = $family_member->flat->flat_no;
            $temp['profile_pic'] = $family_member->user->profile_pic_url;
            $temp['estatus'] = $family_member->estatus;
            array_push($family_member_arr, $temp);
        }

        $data['family_members'] = $family_member_arr;
        $data['total_records'] = $family_members->toArray()['total'];
        return $this->sendResponseWithData($data, "All Family Member Retrieved Successfully.");
    }

    public function get_resident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $family_member = SocietyMember::with('user','flat')->where('society_member_id',$request->society_member_id)->first();
        $data = array();
        $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['user_id'] = $family_member->user->user_id;
            $temp['full_name'] = $family_member->user->full_name;
            $temp['flat_no'] = $family_member->flat->flat_no;
            $temp['profile_pic'] = $family_member->user->profile_pic_url;
            $temp['estatus'] = $family_member->estatus;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "All Folder Retrieved Successfully.");
    }


    public function change_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $family_member = SocietyMember::where('society_member_id',$request->society_member_id)->first();
        if($family_member){
            $family_member->estatus = $request->status;
        }
        $family_member->save();
        return $this->sendResponseSuccess("Status Updated Successfully.");
    }
}
