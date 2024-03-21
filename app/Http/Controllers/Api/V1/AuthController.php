<?php

namespace App\Http\Controllers\APi\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneratedOtp;
use Carbon\Carbon;

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
        $user = User::where('mobile_no',$mobileNo)->where('role',4)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
            $data['otp'] =  mt_rand(100000,999999);
            $user_otp = new GeneratedOtp();
            $user_otp->mobile_no = $mobileNo;
            $user_otp->otp_code = $data['otp'];
            $user_otp->expire_time = Carbon::now();
            $user_otp->save();
            if($user->full_name == ""){
                $data['user_status'] = 'new_user';
            }else{
                $data['user_status'] = 'exist_user';
            }
            $final_data = array();
            array_push($final_data,$data);

            send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
            $data['otp'] =  mt_rand(100000,999999);

            $user = new User();
            $user->mobile_no = $mobile_no;
            $user->role = 4;
            $user->save();

            $user_otp = new GeneratedOtp();
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->referral_id = Str::random(5);
            $user->save();

            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);

            send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }

    public function send_sms(){
        $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=H26o0GZiiEaUyyy0kvOV5g&senderid=MADMRT&channel=2&DCS=0&flashsms=0&number=917622027040&text=Welcome%20to%20Madness%20Mart,%20Your%20One%20time%20verification%20code%20is%205256.%20Regards%20-%20MADNESS%20MART&route=31&EntityId=1301164983812180724&dlttemplateid=1307165088121527950';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
}
