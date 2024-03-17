<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyProfileController extends Controller
{
    public function profile()
    {
        $countries = Country::get();
        $company = CompanyProfile::first();
        return view('admin.company_profile.profile', compact('countries','company'));
    }

    public function update(Request $request)
    {
        $messages = [
            'profile_pic.image' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'profile_pic.mimes' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'company_name.required' => 'Please provide a society name',
            'gst_in_number.required' => 'Please provide a gst number',
            'street_address1.required' => 'Please provide a street address 1',
            'landmark.required' => 'Please provide a landmark',
            'pin_code.required' => 'Please provide a pin code',
            'city_id.required' => 'Please provide a city',
            'state_id.required' => 'Please provide a state',
            'country_id.required' => 'Please provide a country',
        ];
        if(!isset($request->id)){

            $validator = Validator::make($request->all(), [
                'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
                'company_name' => 'required|max:255',
                'gst_in_number' => 'required|regex:/^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}$/',
                'street_address1' => 'required|max:255',
                'street_address2' => 'max:255',
                'landmark' => 'required|max:50',
                'pin_code' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
            ], $messages);
        }else{
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|max:255',
                'gst_in_number' => 'required|regex:/^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}$/',
                'street_address1' => 'required|max:255',
                'street_address2' => 'max:255',
                'landmark' => 'required|max:50',
                'pin_code' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'country_id' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }
        $compnay = CompanyProfile::find(1);
        if (!$compnay) {
            $compnay = new CompanyProfile();
            $old_image = "";
        }else{
            $old_image = $compnay->logo_url;
        }
        $compnay->company_name = $request->company_name;
        $compnay->gst_in_number = $request->gst_in_number;
        $compnay->street_address1 = $request->street_address1;
        $compnay->street_address2 = $request->street_address2;
        $compnay->landmark = $request->landmark;
        $compnay->pin_code = $request->pin_code;
        $compnay->city_id = $request->city_id;
        $compnay->state_id = $request->state_id;
        $compnay->country_id = $request->country_id;
        $compnay->updated_by = Auth::user()->user_id;
        if ($request->hasFile('profile_pic')) {
            $compnay->logo_url = $this->uploadProfileImage($request,$old_image);
        }
        $compnay->save();
        return response()->json(['status' => '200', 'action' => 'add']);
    }

    public function uploadProfileImage($request,$old_image=""){
        $image = $request->file('profile_pic');
        $image_name = 'logo_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('images/logo');
        $image->move($destinationPath, $image_name);
        if(isset($old_image) && $old_image != "") {
            $old_image = public_path($old_image);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        return  'images/logo/'.$image_name;
    }
}
