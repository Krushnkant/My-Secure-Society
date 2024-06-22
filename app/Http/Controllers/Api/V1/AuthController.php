<?php

namespace App\Http\Controllers\Api\V1;

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
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpEmail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class AuthController extends BaseController
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|digits:10'
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->whereIn('user_type', [2, 4])->first();

        if ($user){
            if($user->estatus != 1){
                return $this->sendError(403,"Your account is de-activated by admin.", "Account De-active", []);
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

        $data['otp'] = (int) strval(mt_rand(100000,999999));
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
            'mobile_no' => 'required|digits:10',
            'otp_for' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->whereIn('user_type',[2,4])->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError(403,"Your account is de-activated by admin.", "Account De-active", []);
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
            'mobile_no' => 'required|digits:10',
            'otp' => 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $user = User::with(['societymember.residentdesignationauthority','societymember' => function($query) {
            $query->where('estatus',1);
        }])->where('mobile_no',$request->mobile_no)->whereIn('user_type',[2,4])->first();
        if($user){
        $user_otp = GeneratedOtp::where('mobile_no',$request->mobile_no)->where('otp_code',$request->otp)->first();
            if ($user_otp && isset($user_otp['expire_time']) ){
                $t1 = Carbon::parse(now());
                $t2 = Carbon::parse($user_otp['expire_time']);
                $diff = $t1->diff($t2);
                $user_otp->delete();
                if($diff->i > 30) {
                    return $this->sendError(422,'OTP verification Failed.', "verification Failed", []);
                }
                $authority_array = [];
                if(isset($user->societymember->residentdesignationauthority)){
                    foreach($user->societymember->residentdesignationauthority as $r_auth){
                    $temp['auth'] = $r_auth->auth;
                    $temp['p'] = $r_auth->v.','.$r_auth->a.','.$r_auth->e.','.$r_auth->d.','.$r_auth->p;
                    array_push($authority_array, $temp);
                    }
                }
                $userJwt = ['user_id' => $user->user_id,'block_flat_id'=> isset($user->societymember)?$user->societymember->block_flat_id:"",'society_id'=> isset($user->societymember)?$user->societymember->society_id:"",'society_member_id'=> isset($user->societymember)?$user->societymember->society_member_id:"",'designation_id'=> isset($user->societymember)?$user->societymember->resident_designation_id:"",'authority'=> $authority_array ];
                $data['token'] = JWTAuth::claims($userJwt)->fromUser($user);
                $data['is_new_user'] = $user->full_name == "" ? true : false;
                return $this->sendResponseWithData($data,'OTP verified successfully.');
            }
            else{
                return $this->sendError(422,'OTP verification Failed.', "verification Failed", []);
            }
        }else{
            return $this->sendError(400,'Mobile Number Not Found.', "verification Failed", []);
        }
    }

    public function get_token(Request $request){


        $validator = Validator::make($request->all(), [
            'block_flat_id' => 'required|integer|exists:block_flat,block_flat_id,deleted_at,NULL',
            'firebase_token' => 'required',
            'firebase_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $user_id = Auth::id();
        $block_flat_id = $request->block_flat_id;

        // if(!isFlatInSociety($block_flat_id,$society_id)){
        //     return $this->sendError(422, 'The selected Flat is not associated with the provided Society.', "Validation Errors", []);
        // }


        //$user = User::with('societymember')->where('user_id',$user_id)->first();
        $user = User::with(['societymember.residentdesignationauthority','societymember' => function($query) use ($block_flat_id) {
            $query->where('block_flat_id', $block_flat_id)->where('estatus',1);
        }])->where('user_id', $user_id)->first();
        if($user){
            $authority_array = [];
            if(isset($user->societymember->residentdesignationauthority)){
                foreach($user->societymember->residentdesignationauthority as $r_auth){
                $temp['auth'] = $r_auth->auth;
                $temp['p'] = $r_auth->v.','.$r_auth->a.','.$r_auth->e.','.$r_auth->d.','.$r_auth->p;
                array_push($authority_array, $temp);
                }
            }
            $userJwt = ['user_id' => $user->user_id,'block_flat_id'=> isset($user->societymember)?$user->societymember->block_flat_id:"",'society_id'=> isset($user->societymember)?$user->societymember->society_id:"",'society_member_id'=> isset($user->societymember)?$user->societymember->society_member_id:"",'designation_id'=> isset($user->societymember)?$user->societymember->resident_designation_id:"",'authority'=> $authority_array ];
            $data['token'] = JWTAuth::claims($userJwt)->fromUser($user);

            $user->token = $request->firebase_token;
            $user->firebase_id = $request->firebase_id;
            $user->save();
            return $this->sendResponseWithData($data,'Token get successfully.');
        }else{
            return $this->sendError(400,'User Not Found.', "verification Failed", []);
        }
    }

    public function staff_member_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->where('user_type', 6)->first();

        if (!$user) {
            return $this->sendError(404, 'Staff member not found.', "Authentication Failed", []);
        }

        if ($user->estatus != 1) {
            return $this->sendError(403, "Your account is de-activated by admin.", "Account De-active", []);
        }

        if (!Hash::check($password, $user->password)) {
            return $this->sendError(401, 'Invalid password.', "Authentication Failed", []);
        }

        $authority_array = [];
        if (isset($user->staffmember->residentdesignationauthority)) {
            foreach ($user->staffmember->residentdesignationauthority as $r_auth) {
                $temp['auth'] = $r_auth->auth;
                $temp['p'] = $r_auth->v . ',' . $r_auth->a . ',' . $r_auth->e . ',' . $r_auth->d . ',' . $r_auth->p;
                array_push($authority_array, $temp);
            }
        }

        $userJwt = [
            'user_id' => $user->user_id, // Subject of the token
            'society_id' => $user->staffmember->designation->society_id ?? "",
            'staff_member_id' => $user->staffmember->staff_member_id ?? "",
            'designation_id' => $user->staffmember->resident_designation_id ?? "",
            'authority' => $authority_array
        ];

        try {
            $data['token'] = JWTAuth::claims($userJwt)->fromUser($user);
            return $this->sendResponseWithData($data, 'Login successful.');
        } catch (JWTException $e) {
            return $this->sendError(500, 'Could not create token.', "Token Creation Failed", []);
        } catch (TokenInvalidException $e) {
            return $this->sendError(500, 'Token is invalid.', "Invalid Token", []);
        }
    }

    public function staff_member_forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $email = $request->input('email');
        $user = User::where('email', $email)->where('user_type', 6)->first();

        if (!$user) {
            return $this->sendError(404, 'Staff member not found.', "Forgot Password Failed", []);
        }

        if ($user->estatus != 1) {
            return $this->sendError(403, "Your account is de-activated by admin.", "Account De-active", []);
        }

        $otp = mt_rand(100000, 999999);
        $userOtp = new GeneratedOtp();
        $userOtp->email = $email;
        $userOtp->otp_code = $otp;
        $userOtp->expire_time = Carbon::now()->addMinutes(30);
        $userOtp->save();

        Mail::to($email)->send(new SendOtpEmail("Your OTP code is $otp"));
        return $this->sendResponseWithData(['otp' => $otp], 'OTP sent successfully.');
    }

    public function staff_member_verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
            'new_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $email = $request->input('email');
        $otp = $request->input('otp');

        $userOtp = GeneratedOtp::where('email', $email)
                                ->where('otp_code', $otp)
                                ->orderBy('generated_otp_id', 'desc')
                                ->first();

        if (!$userOtp || $userOtp->expire_time < Carbon::now()) {
            return $this->sendError(422, 'OTP verification failed.', "Verification Failed", []);
        }

        $user = User::where('email', $email)->where('user_type', 6)->first();

        if (!$user) {
            return $this->sendError(404, 'user not found.', "Verification Failed", []);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        $userOtp->delete();

        return $this->sendResponseSuccess('OTP verified successfully.');
    }

    public function staff_member_change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        $user = User::where('user_id', Auth::user()->user_id)->where('user_type', 6)->first();

        if (!$user) {
            return $this->sendError(404, 'user not found.', "Change Password Failed", []);
        }

        if (!Hash::check($old_password, $user->password)) {
            return $this->sendError(401, 'Old password is incorrect.', "Change Password Failed", []);
        }

        $user->password = Hash::make($new_password);
        $user->save();

        return $this->sendResponseSuccess('Password changed successfully.');
    }

}
