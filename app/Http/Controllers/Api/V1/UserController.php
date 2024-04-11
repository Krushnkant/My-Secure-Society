<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
}
