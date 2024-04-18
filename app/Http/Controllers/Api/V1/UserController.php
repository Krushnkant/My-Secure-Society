<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function get_profile(){
        $user_id = Auth::id();
        $user = User::where('user_id',$user_id)->whereIn('user_type', [2, 4])->where('estatus',1)->first();
        if (!$user){
            return $this->sendError(404,"You can not get this profile", "Invalid user", []);
        }
        $data = array();
        array_push($data,new UserResource($user));
        return $this->sendResponseWithData($data, 'User profile Retrieved successfully.');
    }


    public function edit_profile(Request $request){ 
        $user_id = Auth::id();
        $rules = [
            'full_name' => 'required',
            'gender' => ['required', Rule::in([1, 2])],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('user')->where(function ($query) use ($user_id) {
                return $query->where('user_type', 4)->where('user_id','!=',$user_id)->where('estatus','!=',3);
            })],
            // 'mobile_no' => ['required', Rule::unique('user')->where(function ($query) use ($user_id) {
            //     return $query->where('user_type', 4)->where('user_id','!=',$user_id)->where('estatus','!=',3);
            // })],
        ];
        
        if (!empty($request->input('blood_group'))) {
            $rules['blood_group'] = Rule::in(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']);
        }
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $user = User::find($user_id);
        if (!$user)
        {
            return $this->sendError(404,'User Not Exist.', "Not Found Error", []);
        }
        $user->full_name = $request->full_name;
        $user->email = $request->email;
       // $user->mobile_no = $request->mobile_no;
        $user->gender = $request->gender;
        $user->blood_group  = $request->blood_group;
        $user->save();

        return $this->sendResponseWithData(new UserResource($user),'User profile updated successfully.');
    }

    public function update_profilepic(Request $request){ 
        $user_id = Auth::id();
        $rules = [
            'profile_pic' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $user = User::find($user_id);
        if (!$user)
        {
            return $this->sendError(404,'User Not Exist.', "Not Found Error", []);
        }
        if(isset($user->profile_pic)) {
            $old_image = public_path('images/profile_pic/' . $user->profile_pic);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        $image_full_path = "";
        if ($request->hasFile('profile_pic')) { 
            $image = $request->file('profile_pic');
            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            $image_full_path = 'images/profile_pic/'.$image_name;
            $user->profile_pic_url =  $image_full_path;
        }else{
            $user->profile_pic_url =  $image_full_path;
        }
        $user->save();

        return $this->sendResponseWithData(['profile_pic_url' => $image_full_path == "" ?url($image_full_path):""],'User profile pic updated successfully.');
    }

    public function update_coverpic(Request $request){ 
        $user_id = Auth::id();
        $rules = [
            'cover_pic' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $user = User::find($user_id);
        if (!$user)
        {
            return $this->sendError(404,'User Not Exist.', "Not Found Error", []);
        }
        if(isset($user->cover_pic)) {
            $old_image = public_path('images/cover_pic/' . $user->cover_pic);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        $image_full_path = "";
        if ($request->hasFile('cover_pic')) { 
            $image = $request->file('cover_pic');
            $image_name = 'proCoverPic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/cover_pic');
            $image->move($destinationPath, $image_name);
            $image_full_path = 'images/cover_pic/'.$image_name;
            $user->cover_photo_url =  $image_full_path;
        }else{
            $user->profile_pic_url =  $image_full_path;
        }
        $user->save();

        return $this->sendResponseWithData(['profile_pic_url' => $image_full_path == "" ?url($image_full_path):""],'User profile cover pic updated successfully.');
    }

    public function get_country(){
        $countries = Country::get(['country_id','country_name']);
        return $this->sendResponseWithData($countries,"Country Retrieved Successfully.");
    }

    public function get_state($country_id){
        $states = State::where('country_id',$country_id)->get(['state_id','state_name']);
        return $this->sendResponseWithData($states,"State Retrieved Successfully.");
    }

    public function get_city($state_id){
        $cities = City::where('state_id',$state_id)->get(['city_id','city_name']);
        return $this->sendResponseWithData($cities,"City Retrieved Successfully.");
    }
}
