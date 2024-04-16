<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

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
        $family_members = SocietyMember::with('user');
        $family_members = $family_members->orderBy('created_at', 'DESC')->paginate(10);

        $family_member_arr = array();
        foreach ($family_members as $family_member) {
            $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['full_name'] = $family_member->user->full_name;
            $temp['mobile_no'] = $family_member->user->mobile_no;
            $temp['profile_pic'] = $family_member->user->profile_pic_url;
            $temp['is_app_user'] = $family_member->user->usertype == 3 ? true : false;
            array_push($family_member_arr, $temp);
        }

        $data['family_members'] = $family_member_arr;
        $data['total_records'] = $family_members->toArray()['total'];
        return $this->sendResponseWithData($data, "All Family Member Retrieved Successfully.");
    }
}
