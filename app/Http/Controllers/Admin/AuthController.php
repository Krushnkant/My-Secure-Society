<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Helpers;

class AuthController extends Controller
{
    private $page = " Admin";

    public function index()
    {
        return view('admin.auth.login')->with('page',$this->page);
    }

    public function invalid_page()
    {
        return view('admin.403_page');
    }

    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->estatus == 1) {
                return response()->json(['status' => 200]);
            } else {
                Auth::logout(); // Log out the user if their status is not active
                return response()->json(['status' => 300]);
            }
        }
        return response()->json(['status' => 400]);
    }

    public function logout() {
        Session::flush();
        Auth::logout();

        return Redirect('admin');
    }

    public function forgot_password()
    {
        return view('admin.auth.forgot_password')->with('page','forgot-password');
    }

    public function postForgetpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $user = User::where('email',$request->email)->where('estatus',1)->first();
        if ($user){
            $string = Str::random(15);
            $user = User::where('email',$request->email)->first();
            $user->forget_token = $string;
            $user->save();

            $mailData = [
                'message' => url('resetpassword/'.$string)
            ]; 
            $templateName = 'email.ResetPassword';
            $subject = 'Forget Password';
            $mail_sending = Helpers::MailSending($templateName, $mailData, $request->email, $subject);

            return response()->json(['status'=>200]); 
        }    
        return response()->json(['status'=>400]);
    }
}
