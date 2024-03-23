<?php

namespace App\Http\Controllers\APi\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\GeneratedOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|digits:10'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->where('user_type',4)->first();
        
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
        }else{
            $user = new User();
            $user->user_code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $user->mobile_no = $mobile_no;
            $user->user_type = 4;
            $user->created_by = 0;
            $user->updated_by = 0;
            $user->save();
        }

        $user_otp = GeneratedOtp::where('mobile_no', $mobile_no)->orderBy('generated_otp_id', 'desc')->first();
        if ($user_otp && $user_otp->expire_time > Carbon::now()) {
            send_sms($mobile_no, $user_otp->otp_code);
            $data['otp'] =  $user_otp->otp_code;
            return $this->sendResponseWithData($data, 'OTP send successfully.');
        }

        $data['otp'] =  strval(mt_rand(100000,999999));
        $user_otp = new GeneratedOtp();
        $user_otp->mobile_no = $mobile_no;
        $user_otp->otp_code = $data['otp'];
        $user_otp->expire_time = Carbon::now()->addMinutes(30);
        $user_otp->save();
        send_sms($mobile_no, $data['otp']);
        return $this->sendResponseWithData($data, 'OTP send successfully.');
    }

    public function send_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->where('user_type',4)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
        }

        $user_otp = GeneratedOtp::where('mobile_no', $mobile_no)->orderBy('generated_otp_id', 'desc')->first();
        if ($user_otp && $user_otp->expire_time > Carbon::now()) {
            send_sms($mobile_no, $user_otp->otp_code);
            $data['otp'] =  $user_otp->otp_code;
            return $this->sendResponseWithData($data, 'OTP send successfully.');
        }

        $data['otp'] =  mt_rand(100000,999999);
        $user_otp = new GeneratedOtp();
        $user_otp->mobile_no = $mobile_no;
        $user_otp->otp_code = $data['otp'];
        $user_otp->expire_time = Carbon::now()->addMinutes(30);
        $user_otp->save();
        send_sms($mobile_no, $data['otp']);
        return $this->sendResponseWithData($data, 'OTP send successfully.');
    }

    public function verify_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('user_type',4)->first();
        if($user){
        $user_otp = GeneratedOtp::where('mobile_no',$request->mobile_no)->where('otp_code',$request->otp)->first();
            if ($user_otp && isset($user_otp['expire_time']) ){
                $t1 = Carbon::parse(now());
                $t2 = Carbon::parse($user_otp['expire_time']);
                $diff = $t1->diff($t2);
                $user_otp->delete();
                if($diff->i > 30) {
                    return $this->sendError('OTP verification Failed.', "verification Failed", []);
                }
                $userJwt = ['user_id' => $user->user_id];
                $data['token'] = JWTAuth::claims($userJwt)->fromUser($user);
                $data['profile_data'] =  new UserResource($user);
                $data['isNewUser'] = $user->full_name == "" ? true : false;
                return $this->sendResponseWithData($data,'OTP verified successfully.');
            }
            else{
                return $this->sendError('OTP verification Failed.', "verification Failed", []);
            }
        }else{
            return $this->sendError('Mobile Number Not Found.', "verification Failed", []);
        }
    }

   
    public function get_token(Request $request){
        
        $user_id = Auth::id();
        $user = User::where('user_id',$user_id)->first();
        if($user){
            $userJwt = ['user_id' => $user->user_id,'block_flat_id'=> $user->user_id,'society_member_id'=> $user->society_member_id,'authority'=> []];
            $data['token'] = JWTAuth::claims($userJwt)->fromUser($user);
            return $this->sendResponseWithData($data,'get Token successfully.');
           
        }else{
            return $this->sendError('User Not Found.', "verification Failed", []);
        }
    }

}
