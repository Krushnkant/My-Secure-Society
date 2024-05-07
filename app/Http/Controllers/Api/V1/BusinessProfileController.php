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

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle saving the business profile data
        // Assuming the BusinessProfile model has the necessary fields

        $businessProfile = new BusinessProfile();
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

        // Save business icon
        $businessIcon = $request->file('business_icon');
        $businessIconPath = $businessIcon->store('business_icons', 'public');
        $businessProfile->business_icon = $businessIconPath;

        // Save additional image files
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $imageFile) {
                $imageFilePath = $imageFile->store('business_images', 'public');
                // Save image file path to database or perform other actions as needed
            }
        }

        // Save PDF file if provided
        if ($request->hasFile('pdf_file')) {
            $pdfFilePath = $request->file('pdf_file')->store('business_pdfs', 'public');
            // Save PDF file path to database or perform other actions as needed
        }

        // Save the business profile
        $businessProfile->save();

        // Return success response
        return response()->json(['message' => 'Business Profile saved successfully'], 200);
    }
}
