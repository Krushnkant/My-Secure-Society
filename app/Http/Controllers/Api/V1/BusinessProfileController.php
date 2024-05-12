<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class BusinessProfileController extends BaseController
{
    public function get_business_category(Request $request)
    {
        $rules = [
            'parent_category_id' => 'required',
        ];

        if ($request->has('parent_category_id') && $request->input('parent_category_id') != 0) {
            $rules['parent_category_id'] .= '|exists:business_category,business_category_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $query = BusinessCategory::where('estatus', 1);
        if ($request->has('parent_category_id') && $request->parent_category_id != 0) {
            $query->where('parent_business_category_id', $request->parent_category_id);
        } else {
            $query->whereNull('parent_business_category_id');
        }

        $business_categories = $query->orderBy('business_category_name', 'asc')->get();
        $category_arr = array();
        foreach ($business_categories as $category) {
            $temp['category_id'] = $category->business_category_id;
            $temp['parent_category_id'] = $category->parent_business_category_id;
            $temp['category_name'] = $category->business_category_name;
            array_push($category_arr, $temp);
        }
        $data['business_category_list'] = $category_arr;
        return $this->sendResponseWithData($data, "All Business Category Retrieved Successfully.");
    }


    public function saveBusinessProfile(Request $request)
    {
        // Validation rules
        $rules = [
            'profile_id' => 'required',
            'business_name' => 'required|string|max:100',
            'mobile_no' => 'required|string|max:10',
            'website_url' => 'required|url|max:255',
            'business_description' => 'required|string|max:500',
            'street_address1' => 'required|string|max:255',
            'pin_code' => 'required|string',
            'city_id' => 'required|integer',
            'state_id' => 'required|integer',
            'country_id' => 'required|integer',
            'business_icon' => 'required|image', // Assuming business_icon is an image file
            'image_files.*' => 'image', // Assuming image_files is an array of image files
            'pdf_file' => 'file|mimes:pdf|max:10000', // Assuming pdf_file is a PDF file with max size of 10MB
        ];

        if ($request->has('profile_id') && $request->input('profile_id') != 0) {
            $rules['profile_id'] .= '|exists:business_profile,business_profile_id,deleted_at,NULL';
        }

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $businessProfile = BusinessProfile::find($request->contact_id);
        $action = "updated";
        if (!$businessProfile) {
            $businessProfile = new BusinessProfile();
            $action = "saved";
        }
        $businessProfile->business_name = $request->business_name;
        $businessProfile->mobile_no = $request->mobile_no;
        $businessProfile->website_url = $request->website_url;
        $businessProfile->business_description = $request->business_description;
        $businessProfile->street_address1 = $request->street_address1;
        $businessProfile->street_address2 = $request->street_address2;
        $businessProfile->landmark = $request->landmark;
        $businessProfile->pin_code = $request->pin_code;
        $businessProfile->latitude = $request->latitude;
        $businessProfile->longitude = $request->longitude;
        $businessProfile->city_id = $request->city_id;
        $businessProfile->state_id = $request->state_id;
        $businessProfile->country_id = $request->country_id;
        $businessProfile->save();

        // if($businessProfile){
        //   $BusinessPrifileCategory = New BusinessPrifileCategory();
        //   $BusinessPrifileCategory->business_profile_id = $businessProfile->business_profile_id;
        //   $BusinessPrifileCategory->business_profile_id = $request->business_profile_id;
        //   $BusinessPrifileCategory->save();
        // }

        $data = array();
        $temp['profile_id'] = $businessProfile->business_profile_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Business Profile '.$action.' successfully');
    }

    public function list(Request $request)
    {

        $rules = [
            'user_id' => 'required|integer|exists:user,user_id,deleted_at,NULL',
            'list_type' => 'required|in:1,2',
            'category_id' => 'required|integer',
        ];

        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $rules['category_id'] .= '|exists:business_category,business_category_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve business profiles based on parameters
        $query = BusinessProfile::query();
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->list_type == 1) {
            // Own Business Profile List
            $query->where('user_id', $request->user_id);
        }

        $profiles = $query->get();

        // Format the response data
        $data = [];
        foreach ($profiles as $profile) {
            $data[] = [
                'profile_id' => $profile->id,
                'business_name' => $profile->business_name,
                'business_icon' => $profile->business_icon,
                'mobile_no' => $profile->mobile_no,
                'website_url' => $profile->website_url,
                'business_description' => $profile->business_description,
                'street_address1' => $profile->street_address1,
                'street_address2' => $profile->street_address2,
                'landmark' => $profile->landmark,
                'pin_code' => $profile->pin_code,
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
                'city' => $profile->city,
                'state' => $profile->state,
                'country' => $profile->country,
                'user_id' => $profile->user_id,
                'user_fullname' => $profile->user_fullname,
                'user_profile_pic' => $profile->user_profile_pic,
                'user_block_flat_no' => $profile->user_block_flat_no,
            ];
        }

        // Return the response
        return response()->json(['business_profile_list' => $data]);
    }

    public function get(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|exists:business_profile,business_profile_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the specified business profile
        $profile = BusinessProfile::find($request->profile_id);

        // If profile not found, return error response
        if (!$profile) {
            return response()->json(['error' => 'Business profile not found'], 404);
        }

        // Format the response data
        $data = [
            'profile_id' => $profile->id,
                'business_name' => $profile->business_name,
                'business_icon' => $profile->business_icon,
                'mobile_no' => $profile->mobile_no,
                'website_url' => $profile->website_url,
                'business_description' => $profile->business_description,
                'street_address1' => $profile->street_address1,
                'street_address2' => $profile->street_address2,
                'landmark' => $profile->landmark,
                'pin_code' => $profile->pin_code,
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
                'city' => $profile->city,
                'state' => $profile->state,
                'country' => $profile->country,
                'user_id' => $profile->user_id,
                'user_fullname' => $profile->user_fullname,
                'user_profile_pic' => $profile->user_profile_pic,
                'user_block_flat_no' => $profile->user_block_flat_no,
        ];

        return $this->sendResponseWithData($data, "All Profile Retrieved Successfully.");
    }

    public function delete(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|exists:business_profile,business_profile_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Find the profile to delete
        $profile = BusinessProfile::find($request->profile_id);
        $profile->estatus = 3;
        $profile->save();
        $profile->delete();

        // Return success response
        return $this->sendResponseSuccess("Business profile deleted successfully.");
    }
}
