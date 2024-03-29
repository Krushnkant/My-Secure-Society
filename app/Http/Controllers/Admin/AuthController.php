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
use DB;
use Carbon\Carbon;
use Toastr;

class AuthController extends Controller
{
    private $page = "Admin";

    public function index()
    {
        return view('admin.auth.login')->with('page', $this->page);
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
        $request->merge(['user_type' => 1]);
        $credentials = $request->only('email', 'password', 'user_type');
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

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('admin');
    }

    public function forgot_password()
    {
        return view('admin.auth.forgot_password')->with('page', 'forgot-password');
    }

    public function postForgetpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
                    if (!$user || $user->user_type != 1) {
                        $fail('Invalid email or user type not allowed to reset password.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }

        $token = strtoupper(Str::random(15));

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $data = [
            'url' => route('admin.reset_password', $token),
            'logo' => '',
        ];
        $templateName = 'mails.forgetPassword';
        $subject = 'Forget Password';
        $mail_sending = Helpers::MailSending($templateName, $data, $request->email, $subject);

        return response()->json(['status' => 200]);
    }

    public function reset_password($token)
    {
        $data = DB::table('password_reset_tokens')->where(['token' => $token])->first();
        if (!$data || Carbon::parse($data->created_at)->diffInMinutes(Carbon::now()) >= 60) {
            return redirect()->route('admin.login');
        }
        return view('admin.auth.reset_password', ['token' => $token])->with('page', 'reset-password');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|same:confirm_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }
        $updatePassword = DB::table('password_reset_tokens')->where(['token' => $request->token])->first();
        if (!$updatePassword) {
            return response()->json(['status' => 400]);
        }

        $user = User::where('email', $updatePassword->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email' => $updatePassword->email])->delete();


        return response()->json(['status' => 200]);
    }

    public function profile()
    {
        $profile = Auth::user();
        return view('admin.auth.profile', compact('profile'));
    }

    public function updateprofile(Request $request)
    {
        $messages = [
            'profile_pic.image' => 'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'profile_pic.mimes' => 'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'full_name.required' => 'Please provide a FullName',
            'mobile_no.required' => 'Please provide a Mobile No.',
            'email.required' => 'Please provide a Email Address.',
            'password.required' => 'Please provide a Password.',
        ];
        $validator = Validator::make($request->all(), [
            'profile_pic' => 'image|mimes:jpeg,png,jpg',
            'full_name' => 'required',
            'mobile_no' => 'required|numeric|digits:10',
            'email' => 'required|email',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }

        $user = Auth::user();
        $old_image = $user->profile_pic_url;
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->gender = $request->gender;
        $user->updated_by = Auth::user()->user_id;
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            if (isset($old_image) && $old_image != "") {
                $old_image = public_path($old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $user->profile_pic_url  = 'images/profile_pic/' . $image_name;
        }
        $user->save();
        return response()->json(['status' => '200']);
    }

    public function changepassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|same:confirm_password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => '200']);
    }
}
