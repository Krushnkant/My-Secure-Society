<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use App\Models\BusinessProfile;
use App\Models\BusinessProfileFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isEmpty;

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
            $query->where('parent_business_category_id',0);
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


    public function save_business_profile(Request $request)
    {

        Validator::extend('valid_state', function ($attribute, $value, $parameters, $validator) {
            $countryId = $parameters[0];
            return \DB::table('state')->where('state_id', $countryId)->where('country_id', $value)->exists();
        });

        Validator::extend('valid_city', function ($attribute, $value, $parameters, $validator) {
            $stateId = $parameters[0];
            return \DB::table('city')->where('city_id', $stateId)->where('state_id', $value)->exists();
        });

        Validator::extend('exists_in_business_profile_file', function ($attribute, $value, $parameters, $validator) {
            return \DB::table('business_profile_file')->whereIn('business_profile_file_id', $value)->exists();
        });

        // Validation rules
        $rules = [
            'profile_id' => 'required',
            'category_id' => 'required|exists:business_category,business_category_id,deleted_at,NULL',
            'business_name' => 'required|string|max:100',
            'mobile_no' => 'required|string|max:10',
            'website_url' => 'required|url|max:255',
            'business_description' => 'required|string|max:500',
            'street_address1' => 'required|string|max:255',
            'pin_code' => 'required|integer',
            'city_id' => 'required|integer|exists:city,city_id',
            'state_id' => 'required|integer|exists:state,state_id|valid_city:' . $request->city_id,
            'country_id' => 'required|integer|exists:country,country_id|valid_state:' . $request->state_id,
            'business_icon' => 'required|image', // Assuming business_icon is an image file
            'image_files' => 'required|array|min:1|max:5',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:2048',
        ];

        if ($request->has('profile_id') && $request->input('profile_id') != 0) {
            if($request->deleted_file_id[0] != ""){
                $rules['deleted_file_id'] .= 'nullable|array|exists_in_business_profile_file';
            }
        }

        if ($request->has('profile_id') && $request->input('profile_id') != 0) {
            $rules['profile_id'] .= '|exists:business_profile,business_profile_id,deleted_at,NULL,created_by,'.auth()->id();
        }

        $messages = [
            'valid_state' => 'The selected state does not belong to the specified country.',
            'valid_city' => 'The selected city does not belong to the specified state.',
            'exists_in_business_profile_file' => 'One or more selected files for deletion do not exist.',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules,$messages);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $businessProfile = BusinessProfile::find($request->profile_id);
        $action = "updated";
        if (!$businessProfile) {
            $businessProfile = new BusinessProfile();
            $action = "saved";
            $businessProfile->created_by = Auth::user()->user_id;
        }
        $businessProfile->updated_by = Auth::user()->user_id;
        $businessProfile->business_category_id = $request->category_id;
        $businessProfile->business_name = $request->business_name;
        $businessProfile->phone_number = $request->mobile_no;
        $businessProfile->website_url = $request->website_url;
        $businessProfile->description = $request->business_description;
        $businessProfile->street_address1 = $request->street_address1;
        $businessProfile->street_address2 = $request->street_address2;
        $businessProfile->landmark = $request->landmark;
        $businessProfile->pin_code = $request->pin_code;
        $businessProfile->latitude = $request->latitude;
        $businessProfile->longitude = $request->longitude;
        $businessProfile->city_id = $request->city_id;
        $businessProfile->state_id = $request->state_id;
        $businessProfile->country_id = $request->country_id;
        if ($request->hasFile('business_icon')) {
            if(isset($businessProfile->business_icon)) {
                $old_image = public_path('images/business_icon/' . $businessProfile->business_icon);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $image = $request->file('business_icon');
            $image_full_path = UploadImage($image,'images/business_icon');
            $businessProfile->business_icon =  $image_full_path;
        }

        $businessProfile->save();

        if($businessProfile){

            if ($request->hasFile('image_files')) {
                $files = $request->file('image_files');
                foreach ($files as $file) {
                    $fileType = getFileType($file);
                    $fileUrl = UploadImage($file, 'images/business');
                    $this->storeFileEntry($businessProfile->business_profile_id, $fileType, $fileUrl);
                }
            }

             // Handle file upload for PDF
            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $fileType = getFileType($file);
                $fileUrl = UploadImage($file,'images/business');
                $this->storeFileEntry($businessProfile->business_profile_id, $fileType, $fileUrl);
            }

            // Handle deletion of files
            if($action == "updated"){
                if ($request->has('deleted_file_id')) {
                    $deletedFileIds = $request->input('deleted_file_id');
                    foreach ($deletedFileIds as $fileId) {
                        $fileEntry = BusinessProfileFile::find($fileId);
                        if ($fileEntry) {
                            $filePath = public_path($fileEntry->file_url);
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            $fileEntry->delete();
                        }
                    }
                }
            }

        //   $BusinessPrifileCategory = New BusinessProfileCategory();
        //   $BusinessPrifileCategory->business_profile_id = $businessProfile->business_profile_id;
        //   $BusinessPrifileCategory->business_profile_id = $request->business_profile_id;
        //   $BusinessPrifileCategory->save();
        }

        $data = array();
        $temp['profile_id'] = $businessProfile->business_profile_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Business Profile '.$action.' successfully');
    }

    public function storeFileEntry($Id, $fileType, $fileUrl)
    {
        $fileEntry = new BusinessProfileFile();
        $fileEntry->business_profile_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now();
        $fileEntry->save();
    }

    public function business_profile_list(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer',
           // 'list_type' => 'required|in:1,2',
            'category_id' => 'required|integer',
        ];
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $rules['category_id'] .= '|exists:business_category,business_category_id,deleted_at,NULL';
        }
        if ($request->has('user_id') && $request->input('user_id') != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }
        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Retrieve business profiles based on parameters
        $query = BusinessProfile::with('user','city','state','country')->where('estatus',1);
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $query->where('business_category_id', $request->category_id);
        }

        //if ($request->list_type == 1) {
            // Own Business Profile List

        //}
        if ($request->has('user_id') && $request->input('user_id') != 0) {
            $query->where('created_by', $request->user_id);
        }
        $query->orderBy('business_name', 'ASC');
        $perPage = 10;
        $profiles = $query->paginate($perPage);

        // Format the response data
        $profile_arr = [];
        foreach ($profiles as $profile) {
            $temp['profile_id'] = $profile->business_profile_id;
            $temp['business_name'] = $profile->business_name;
            $temp['category_name'] = isset($profile->business_category)?$profile->business_category->business_category_name:"";
            $temp['business_icon'] = $profile->business_icon != ""?url($profile->business_icon):"";
            $temp['mobile_no'] = $profile->phone_number;
            $temp['website_url'] = $profile->website_url;
            $temp['business_description'] = $profile->description;
            $temp['street_address1'] = $profile->street_address1;
            $temp['street_address2'] = $profile->street_address2;
            $temp['landmark'] = $profile->landmark;
            $temp['pin_code'] = $profile->pin_code;
            $temp['latitude'] = $profile->latitude;
            $temp['longitude'] = $profile->longitude;
            $temp['city'] = isset($profile->city)?$profile->city->city_name:"";
            $temp['state'] = isset($profile->state)?$profile->state->state_name:"";
            $temp['country'] = isset($profile->state)?$profile->country->country_name:"";
            $temp['user_id'] = isset($profile->user)?$profile->user->user_id:"";
            $temp['user_fullname'] = isset($profile->user)?$profile->user->full_name:"";
            $temp['user_profile_pic'] = isset($profile->user) && $profile->profile_pic_url != ""?url($profile->user->profile_pic_url):"";
            $temp['user_block_flat_no'] = getUserBlockAndFlat($profile->created_by);
            array_push($profile_arr, $temp);
        }

        $data['business_profile_list'] = $profile_arr;
        $data['total_records'] = $profiles->toArray()['total'];
        return $this->sendResponseWithData($data, "All business profile Successfully.");
    }

    public function get_business_profile(Request $request)
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
        $profile = BusinessProfile::with('image_files','pdf_file','user')->find($request->profile_id);

        // If profile not found, return error response
        if (!$profile) {
            return response()->json(['error' => 'Business profile not found'], 404);
        }

        $image_files = [];
        foreach ($profile->image_files as $image_file) {
            $file_temp['file_id'] = $image_file->business_profile_file_id;
            $file_temp['file_url'] = url($image_file->file_url);
            array_push($image_files, $file_temp);
        }

        $pdf_file = [];
        if(isset($profile->pdf_file) && $profile->pdf_file != null){
            $pdf_temp['file_id'] = $profile->pdf_file->business_profile_file_id;
            $pdf_temp['file_type'] = $profile->pdf_file->file_type;
            $pdf_temp['file_url'] = url($profile->pdf_file->file_url);
            array_push($pdf_file, $pdf_temp);
        }
        $data = array();
        $temp['profile_id'] = $profile->business_profile_id;
        $temp['business_name'] = $profile->business_name;
        $temp['business_icon'] = $profile->business_icon != ""?url($profile->business_icon):"";
        $temp['mobile_no'] = $profile->phone_number;
        $temp['website_url'] = $profile->website_url;
        $temp['business_description'] = $profile->description;
        $temp['street_address1'] = $profile->street_address1;
        $temp['street_address2'] = $profile->street_address2;
        $temp['landmark'] = $profile->landmark;
        $temp['pin_code'] = $profile->pin_code;
        $temp['latitude'] = $profile->latitude;
        $temp['longitude'] = $profile->longitude;
        $temp['city'] = $profile->city_id;
        $temp['state'] = $profile->state_id;
        $temp['country'] = $profile->country_id;
        $temp['user_id'] = isset($profile->user)?$profile->user->user_id:"";
        $temp['user_fullname'] = isset($profile->user)?$profile->user->full_name:"";
        $temp['user_profile_pic'] = isset($profile->user) && $profile->profile_pic_url != ""?url($profile->user->profile_pic_url):"";
        $temp['user_block_flat_no'] = getUserBlockAndFlat($profile->created_by);
        $temp['image_files'] = $image_files;
        $temp['pdf_file'] = $profile->pdf_file;

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All Profile Retrieved Successfully.");
    }

    public function delete_business_profile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|integer|exists:business_profile,business_profile_id,deleted_at,NULL,created_by,'.auth()->id(),
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
