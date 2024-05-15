<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyHelpService;
use App\Models\ServiceProviderFile;
use App\Models\ServiceProvider;
use App\Models\SocietyVisitor;
use App\Models\ServiceProviderWorkFla;
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

        if ($request->has('daily_help_provider_id') && $request->input('daily_help_provider_id') != 0) {
            $rules['daily_help_provider_id'] .= '|exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL';
        }

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

    public function service_provider_list(Request $request)
    {
        $rules = [
            'daily_help_service_id' => 'required|integer|exists:daily_help_service,daily_help_service_id,deleted_at,NULL',
            'rating' => 'required|in:1,2,3,4,5,6',
        ];

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Retrieve business profiles based on parameters
        $query = ServiceProvider::with('user','daily_help_service')->where('estatus',1)->where('daily_help_service_id',$request->daily_help_service_id);
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search_text')) {
            $searchText = $request->input('search_text');

            $query->whereHas('user', function ($userQuery) use ($searchText) {
                $userQuery->where('mobile_no', 'LIKE', "%$searchText%")
                          ->orWhere('user_code', 'LIKE', "%$searchText%")
                          ->orWhere('full_name', 'LIKE', "%$searchText%");
            });
        }

        $perPage = 10;
        $providers = $query->paginate($perPage);

        // Format the response data
        $provider_arr = [];
        foreach ($providers as $provider) {
            $temp['daily_help_provider_id'] = $provider->daily_help_provider_id;
            $temp['daily_help_user_id'] = $provider->user_id;
            $temp['full_name'] = isset($profile->user)?$profile->user->full_name:"";
            $temp['mobile_no'] = isset($profile->user)?$profile->user->mobile_no:"";
            $temp['profile_pic'] = isset($profile->user) && $profile->profile_pic_url != ""?url($profile->user->profile_pic_url):"";
            $temp['service_name'] = isset($profile->daily_help_service)?$profile->daily_help_service->service_name:"";
            $temp['service_icon'] = isset($profile->user)?$profile->user->service_icon:"";
            $temp['rating'] = "";
            array_push($provider_arr, $temp);
        }

        $data['provider_list'] = $provider_arr;
        $data['total_records'] = $providers->toArray()['total'];
        return $this->sendResponseWithData($data, "All Provider Successfully.");
    }

    public function get_service_provider(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'daily_help_provider_id' => 'required||exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the specified business profile
        $provider = ServiceProvider::with('user','daily_help_service','front_img','back_img')->find($request->daily_help_provider_id);

        // If profile not found, return error response
        if (!$provider) {
            return response()->json(['error' => 'provider not found'], 404);
        }


        $data = array();
        $temp['daily_help_provider_id'] = $provider->daily_help_provider_id;
        $temp['daily_help_user_id'] = $provider->user_id;
        $temp['daily_help_user_passcode'] = isset($profile->user)?$profile->user->user_code:"";
        $temp['full_name'] = isset($profile->user)?$profile->user->full_name:"";
        $temp['mobile_no'] = isset($profile->user)?$profile->user->mobile_no:"";
        $temp['profile_pic'] = isset($profile->user) && $profile->profile_pic_url != ""?url($profile->user->profile_pic_url):"";
        $temp['gender'] = isset($profile->user)?$profile->user->gender:"";
        $temp['service_name'] = isset($profile->daily_help_service)?$profile->daily_help_service->service_name:"";
        $temp['service_icon'] = isset($profile->user)?$profile->user->service_icon:"";
        $temp['rating'] = "";
        $temp['indentity_proof_front_img'] = isset($profile->front_img) ? url($profile->front_img->file_url):"";
        $temp['indentity_proof_back_img'] = isset($profile->back_img) ?url($profile->back_img->file_url):"";
        $temp['can_add_review'] = "";
        $temp['work_in_flats'] = "";
        $temp['block_flat_no'] = "";
        $temp['work_time'] = "";


        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All provider Retrieved Successfully.");
    }

    public function delete_service_provider(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'daily_help_provider_id' => 'required||exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Find the profile to delete
        $provider = ServiceProvider::find($request->daily_help_provider_id);
        $provider->estatus = 3;
        $provider->save();
        $provider->delete();

        // Return success response
        return $this->sendResponseSuccess("Business provider deleted successfully.");
    }


    public function service_provider_add_flat(Request $request)
    {
        $block_flat_id = $this->payload['block_flat_id'];
        if($block_flat_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $rules = [
            'daily_help_provider_id' => [
                'required',
                'exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL'
            ],
            'from_time' => [
                'required',
                'date_format:H:i:s'
            ],
            'to_time' => [
                'required',
                'date_format:H:i:s',
                'after:from_time'
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        
        $work = New ServiceProviderWorkFlat();
        $work->daily_help_provider_id = $request->daily_help_provider_id;
        $work->block_flat_id = $block_flat_id;
        $work->work_start_time = $request->from_time;
        $work->work_end_time = $request->to_time;
        $work->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $work->created_by = Auth::user()->user_id;
        $work->updated_by = Auth::user()->user_id;
        $work->save();
        
        if($work){
            $visitor = New SocietyVisitor();
            $visitor->daily_help_provider_id = $request->daily_help_provider_id;
            $visitor->block_flat_id = $block_flat_id;
            $visitor->work_start_time = $request->from_time;
            $visitor->work_end_time = $request->to_time;
            $visitor->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $visitor->created_by = Auth::user()->user_id;
            $visitor->updated_by = Auth::user()->user_id;
            $visitor->save();
        }

        $data = array();
        $temp['work_flat_id'] = $work->work_flat_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Service Provider Work Flat added successfully');
    }
}
