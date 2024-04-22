<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class FamilyMemberController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }
    
    public function save_family(Request $request)
    {
        $society_member_id = $this->payload['society_member_id'];
        if($society_member_id == ""){
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
        $request->merge(['society_member_id'=>$society_member_id]);
        $rules = [
            'profile_pic' => $request->has('profile_pic') ? 'image|mimes:jpeg,png,jpg' : '',
            'full_name' => 'required|max:70',
            'society_member_id' => 'required|exists:society_member',
        ];
        if ($request->has('user_id') && $request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->ignore($request->user_id,'user_id')->whereIn('user_type',[2,3])->whereNull('deleted_at'),
            ];
        } elseif ($request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->whereIn('user_type',[2,3])->whereNull('deleted_at'),
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->user_id == 0){
            $user = New User();
            $user->full_name = $request->full_name;
            if ($request->hasFile('profile_pic')) { 
                $image = $request->file('profile_pic');
                $user->profile_pic_url = UploadImage($image,'images/profile_pic');
            }
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $user = User::find($request->user_id);
            $old_image = $user->profile_pic_url;
            $user->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            if ($request->hasFile('profile_pic')) { 
                $image = $request->file('profile_pic');
                $user->profile_pic_url = UploadImage($image,'images/profile_pic');
            }
            $user->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }
         
        $user->full_name = $request->full_name;
        $user->user_code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $user->user_type = 2;
        $user->mobile_no = $request->mobile_no;
        $user->save();

        if($user && $request->user_id == 0){
            $main_society_member = SocietyMember::find($society_member_id);
            $society_member = new SocietyMember();
            $society_member->user_id = $user->user_id;
            $society_member->parent_society_member_id  = $main_society_member->society_member_id;
            $society_member->society_id = $main_society_member->society_id;
            $society_member->resident_designation_id = 3;
            $society_member->block_flat_id = $main_society_member->block_flat_id;
            $society_member->resident_type = $main_society_member->resident_type;
            $society_member->created_by = Auth::id();
            $society_member->updated_by = Auth::id();
            $society_member->save();
        }

        return $this->sendResponseSuccess("Family Member ".$action." Successfully");
    }

   

    public function family_list(Request $request)
    {
        $society_member_id = $this->payload['society_member_id'];
        if($society_member_id == ""){
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
        $family_members = SocietyMember::with('user')->where('parent_society_member_id', $society_member_id);
        $family_members = $family_members->orderBy('created_at', 'DESC')->paginate(10);

        $family_member_arr = array();
        foreach ($family_members as $family_member) {
            $temp['society_member_id'] = $family_member['society_member_id'];
            $temp['user_id'] = $family_member['user_id'];
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

    public function delete_family_member(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $society_member = SocietyMember::find($request->society_member_id);
        if ($society_member) {
            $society_member->estatus = 3;
            $society_member->save();
            $society_member->delete();
        }
        return $this->sendResponseSuccess("member deleted Successfully.");
    }
}
