<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyHelpService;
use App\Models\ServiceProviderFile;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceProviderControler extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }


    public function daily_help_service_list()
    {
        $services = DailyHelpService::where('estatus', 1)->orderBy('service_name', 'asc')->get();
        $service_arr = array();
        foreach ($services as $service) {
            $temp['daily_help_service_id'] = $service->daily_help_service_id;
            $temp['service_name'] = $service->service_name;
            $temp['service_icon'] = $service->service_icon;
            array_push($service_arr, $temp);
        }
        $data['service_list'] = $service_arr;
        return $this->sendResponseWithData($data, "All Service Retrieved Successfully.");
    }

    public function save_service_provider(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $rules = [
            'daily_help_provider_id' => 'required',
            'daily_help_service_id' => 'required|exists:daily_help_service,daily_help_service_id,deleted_at,NULL',
            'full_name' => 'required|string|max:100',
            'mobile_no' => 'required|digits:10',
            'gender' => ['required', Rule::in([1, 2])],
            'profile_pic' => 'image|mimes:jpeg,png,jpg',
            'indentity_proof_front_img' => 'image|mimes:jpeg,png,jpg',
            'indentity_proof_back_img' => 'image|mimes:jpeg,png,jpg',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->daily_help_provider_id == 0){
            $user = new User();
            $user->created_by = Auth::user()->user_id;
            $user->user_code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $user->full_name = $request->full_name;
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $image_full_path = "";
            if ($request->hasFile('profile_pic')) { 
                $image = $request->file('profile_pic');
                $image_full_path = UploadImage($image,'images/profile_pic');
            }
            $user->profile_pic_url =  $image_full_path;
            $user->save();

            if($user){
                $serviceProvider = new ServiceProvider();
                $serviceProvider->created_by = Auth::user()->user_id;
                $serviceProvider->society_id = $society_id;
                $serviceProvider->user_id = $user->user_id;
                $serviceProvider->daily_help_service_id = $request->daily_help_service_id;
                $serviceProvider->save();
            }
        }else{
            $serviceProvider = ServiceProvider::find($request->post_id);
            if($serviceProvider){
                $user = User::find($serviceProvider->user_id);
                $user->updated_by = Auth::user()->user_id;
                $user->full_name = $request->full_name;
                $user->mobile_no = $request->mobile_no;
                $user->gender = $request->gender;
                if(isset($user->profile_pic)) {
                    $old_image = public_path('images/profile_pic/' . $user->profile_pic);
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                $image_full_path = "";
                if ($request->hasFile('profile_pic')) { 
                    $image = $request->file('profile_pic');
                    $image_full_path = UploadImage($image,'images/profile_pic');
                }
                $user->profile_pic_url =  $image_full_path;
                $user->save();
            }
            $serviceProvider->daily_help_service_id = $request->daily_help_service_id;
            $serviceProvider->save();
        }

        if ($request->hasFile('indentity_proof_front_img')) {
            // if(isset($businessProfile->business_icon)) {
            //     $old_image = public_path('images/profile_pic/' . $user->business_icon);
            //     if (file_exists($old_image)) {
            //         unlink($old_image);
            //     }
            // }
            $image = $request->file('indentity_proof_front_img');
            $fileUrl = UploadImage($image,'images/provider_indentity_proof');
            $this->storeFileEntry($serviceProvider->daily_help_provider_id, $fileType, $fileUrl,1);
        }

        if ($request->hasFile('indentity_proof_back_img')) {
            $image = $request->file('indentity_proof_back_img');
            $fileUrl = UploadImage($image,'images/provider_indentity_proof');
            $this->storeFileEntry($serviceProvider->daily_help_provider_id, $fileType, $fileUrl,2);
        }

        $data = array();
        $temp['daily_help_provider_id'] = $businessProfile->daily_help_provider_id;
        $temp['passcode'] = $user->user_code;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Service Provider successfully');
    }

    public function storeFileEntry($Id, $fileType, $fileUrl,$fileView)
    {
        $fileEntry = new ServiceProviderFile();
        $fileEntry->daily_help_provider_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_view = $fileView;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now();
        $fileEntry->save();
    }

    public function business_profile_list(Request $request)
    {

        $rules = [
            'user_id' => 'required|integer',
            'list_type' => 'required|in:1,2',
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
        $query = BusinessProfile::with('user')->where('estatus',1);
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->list_type == 1) {
            // Own Business Profile List
            $query->where('created_by', $request->user_id);
        }
        $perPage = 10;
        $profiles = $query->paginate($perPage);

        // Format the response data
        $profile_arr = [];
        foreach ($profiles as $profile) {
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
            $temp['user_block_flat_no'] = "";
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
            $file_temp['business_profile_id'] = $image_file->business_profile_id;
            $file_temp['file_type'] = $image_file->file_type;
            $file_temp['file_url'] = url($image_file->file_url);
            array_push($image_files, $file_temp);
        }

        $pdf_file = [];
        if(isset($profile->pdf_file) && $profile->pdf_file != null){
            $pdf_temp['business_profile_id'] = $profile->pdf_file->business_profile_id;
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
        $temp['user_block_flat_no'] = "";
        $temp['image_files'] = $image_files;
        $temp['pdf_file'] = $profile->pdf_file;

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All Profile Retrieved Successfully.");
    }

    public function delete_business_profile(Request $request)
    {

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
