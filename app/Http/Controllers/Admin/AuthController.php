<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $page = "Gemver Admin";

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
            'Email' => 'required|email',
            'Password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }

        $credentials = $request->only('Email', 'Password');
        
       
            if (Auth::attempt($credentials)) {
                $user = User::where('Email', $credentials['Email'])->first();
            if ($user->eStatus == 1) {
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
}
