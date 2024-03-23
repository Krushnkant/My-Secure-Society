<?php

namespace App\Http\Controllers\APi\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneratedOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobileNo' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobileNo = $request->mobileNo;
        $user = User::where('mobile_no',$mobileNo)->where('user_type',4)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
        }

        $user_otp = GeneratedOtp::where('mobile_no', $mobileNo)->orderBy('generated_otp_id', 'desc')->first();
        if ($user_otp && $user_otp->expire_time > Carbon::now()) {
            send_sms($mobileNo, $user_otp->otp_code);
            $data['otp'] =  $user_otp->otp_code;
            return $this->sendResponseWithData($data, 'OTP send successfully.');
        }

        $data['otp'] =  mt_rand(100000,999999);
        $user_otp = new GeneratedOtp();
        $user_otp->mobile_no = $mobileNo;
        $user_otp->otp_code = $data['otp'];
        $user_otp->expire_time = Carbon::now()->addMinutes(30);
        $user_otp->save();
        send_sms($mobileNo, $data['otp']);
        return $this->sendResponseWithData($data, 'OTP send successfully.');
    }

    public function verify_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobileNo' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('otp',$request->otp)->where('estatus',1)->first();

        if ( $user && isset($user['otp_created_at']) ){
            $t1 = Carbon::parse(now());
            $t2 = Carbon::parse($user['otp_created_at']);
            $diff = $t1->diff($t2);
//            dd(Carbon::now()->toDateTimeString(),$user['otp_created_at'],$diff->i);
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            if($diff->i > 30) {
                return $this->sendError('OTP verification Failed.', "verification Failed", []);
            }

            $data['token'] =  $user->createToken('MyApp')-> accessToken;
            $data['profile_data'] =  new UserResource($user);
            $final_data = array();
            array_push($final_data,$data);
            return $this->sendResponseWithData($final_data,'OTP verified successfully.');
        }
        else{
            return $this->sendError('OTP verification Failed.', "verification Failed", []);
        }
    }


    public function edit_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'email' => ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->where('role', 3)->where('id','!=',$request->user_id)->where('estatus','!=',3);
            })],

        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $premiumuserid = User::whereNotNull('premiumuserid')->where('role',3)->orderBy('id', 'DESC')->first();
            //dd($premiumuserid);
        $preserid = 1;
        if($premiumuserid){
            $preserid  = $premiumuserid->premiumuserid + 1;
        }


        $user = User::find($request->user_id);
        if (!$user)
        {
            return $this->sendError('User Not Exist.', "Not Found Error", []);
        }
        if($user->premiumuserid == "" && $user->premiumuserid == null){
            $user->premiumuserid = $preserid;
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $request->first_name." ".$request->last_name;
        $user->dob = $request->dob;
        if (isset($request->gender)) {
            $user->gender = $request->gender;
        }
        $user->email = isset($request->email) ? $request->email : null;

        if ($request->hasFile('profile_pic')) {
            if(isset($user->profile_pic)) {
                $old_image = public_path('images/profile_pic/' . $user->profile_pic);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }

            $image = $request->file('profile_pic');
            $ext = $image->getClientOriginalExtension();
            $ext = strtolower($ext);
            // $all_ext = array("png","jpg", "jpeg", "jpe", "jif", "jfif", "jfi","tiff","tif","raw","arw","svg","svgz","bmp", "dib","mpg","mp2","mpeg","mpe");
            $all_ext = array("png", "jpg", "jpeg");
            if (!in_array($ext, $all_ext)) {
                return $this->sendError('Invalid type of image.', "Extension error", []);
            }

            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            $user->profile_pic = $image_name;
        }
        $user->save();

        return $this->sendResponseWithData(new UserResource($user),'User profile updated successfully.');
    }

}
