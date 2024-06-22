<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyHelpService;
use App\Models\ServiceProviderFile;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderReview;
use App\Models\ServiceProviderWorkFlat;
use App\Models\User;
use App\Models\UserRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use DateTime;
use Illuminate\Support\Carbon;

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
            'user_id' => 'required|integer',
            // 'daily_help_service_id' => 'required|exists:daily_help_service,daily_help_service_id,deleted_at,NULL',
            'full_name' => 'required|string|max:50',
            'gender' => ['required', Rule::in([1, 2])],
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
            'indentity_proof_front_img' => 'required|image|mimes:jpeg,png,jpg',
            'indentity_proof_back_img' => 'required|image|mimes:jpeg,png,jpg',
            'service_list' => 'required|array|min:1',
            'service_list.*.daily_help_service_id' => 'required|exists:daily_help_service,daily_help_service_id,deleted_at,NULL',
            'service_list.*.daily_help_provider_id' => 'nullable|integer',
            'service_list.*.is_deleted' => 'required|in:1,2',
        ];

        if ($request->has('user_id') && $request->input('user_id') != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }

        if ($request->user_id > 0) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->where('user_type',5)->ignore($request->user_id,'user_id')->whereNull('deleted_at'),
            ];
        } else {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->where('user_type',5)->whereNull('deleted_at'),
            ];
        }

        $messages = [
            'service_list.*.daily_help_service_id.required' => 'The daily_help_service_id field is required.',
            'service_list.*.is_deleted.required' => 'The is_deleted field is required.',
            'service_list.*.daily_help_provider_id.required' => 'The selected service_list daily_help_provider_id is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

         // Add conditional validation for daily_help_provider_id
        $validator->sometimes('service_list.*.daily_help_provider_id', 'integer|exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL', function ($input) {
            return isset($input->service_list) && is_array($input->service_list) && collect($input->service_list)->contains(function ($serviceData) {
                return !empty($serviceData['daily_help_provider_id']) && $serviceData['daily_help_provider_id'] != 0;
            });
        });


        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        DB::beginTransaction();
        try {

        if($request->user_id == 0){
            $user = new User();
            $user->user_code = rand(100000, 999999);
            $user->full_name = $request->full_name;
            $user->mobile_no = $request->mobile_no;
            $user->user_type = 5;
            $user->gender = $request->gender;
            $image_full_path = "";
            if ($request->hasFile('profile_pic')) {
                $image = $request->file('profile_pic');
                $image_full_path = UploadImage($image,'images/profile_pic');
            }
            $user->profile_pic_url =  $image_full_path;
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $user->save();

            // if($user){
            //     $serviceProvider = new ServiceProvider();
            //     $serviceProvider->society_id = $society_id;
            //     $serviceProvider->user_id = $user->user_id;
            //     $serviceProvider->daily_help_service_id = $request->daily_help_service_id;
            //     $serviceProvider->created_by = Auth::user()->user_id;
            //     $serviceProvider->updated_by = Auth::user()->user_id;
            //     $serviceProvider->save();
            // }
        }else{

                $user = User::find($request->user_id);
                $designation_id = $this->payload['designation_id'];
                if(getResidentDesignation($designation_id) == "Society Member" &&  $user->created_by != auth()->id()){
                    return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
                }

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
                $user->updated_by = Auth::user()->user_id;
                $user->save();
                // $serviceProvider = ServiceProvider::where('user_id',$user->user_id)->first();
                // if($serviceProvider){
                //     $serviceProvider->daily_help_service_id = $request->daily_help_service_id;
                //     $serviceProvider->updated_by = Auth::user()->user_id;
                //     $serviceProvider->save();
                // }
        }

        // Process service_list
        foreach ($request->service_list as $serviceData) {
            if ($serviceData['is_deleted'] == 2) {
                if (isset($serviceData['daily_help_provider_id']) && $serviceData['daily_help_provider_id'] != "" && $serviceData['daily_help_provider_id'] != 0) {
                    $service = ServiceProvider::find($serviceData['daily_help_provider_id']);
                    $service->daily_help_service_id = $serviceData['daily_help_service_id'];
                    $service->updated_by = Auth::user()->user_id;
                    $service->save();
                } else {
                    $service = new ServiceProvider();
                    $service->society_id = $society_id;
                    $service->user_id = $user->user_id;
                    $service->daily_help_service_id = $serviceData['daily_help_service_id'];
                    $service->created_by = Auth::user()->user_id;
                    $service->updated_by = Auth::user()->user_id;
                    $service->save();
                }

                if ($request->hasFile('indentity_proof_front_img')) {
                    // if(isset($businessProfile->business_icon)) {
                    //     $old_image = public_path('images/profile_pic/' . $user->business_icon);
                    //     if (file_exists($old_image)) {
                    //         unlink($old_image);
                    //     }
                    // }
                    $image = $request->file('indentity_proof_front_img');
                    $fileType = getFileType($image);
                    $fileUrl = UploadImage($image,'images/provider_indentity_proof');
                    $this->storeFileEntry($service->daily_help_provider_id, $fileType, $fileUrl,1);
                }

                if ($request->hasFile('indentity_proof_back_img')) {
                    $image = $request->file('indentity_proof_back_img');
                    $fileType = getFileType($image);
                    $fileUrl = UploadImage($image,'images/provider_indentity_proof');
                    $this->storeFileEntry($service->daily_help_provider_id, $fileType, $fileUrl,2);
                }
            } elseif ($serviceData['is_deleted'] == 1 && isset($serviceData['daily_help_provider_id'])) {
                ServiceProvider::destroy($serviceData['daily_help_provider_id']);
            }
        }


        DB::commit();

        $data = array();
        $temp['user_id'] = $user->user_id;
        $temp['passcode'] = $user->user_code;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Service Provider successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while saving the service provider.', "Internal Server Error", []);
        }
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
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Retrieve service providers based on parameters
        $query = ServiceProvider::with(['user', 'daily_help_service','user_rating'])
            ->where('estatus', 1)
            ->where('daily_help_service_id', $request->daily_help_service_id);

        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search_text')) {
            $searchText = $request->input('search_text');
            $query->whereHas('user', function (Builder $userQuery) use ($searchText) {
                $userQuery->where('mobile_no', 'LIKE', "%$searchText%")
                        ->orWhere('user_code', 'LIKE', "%$searchText%")
                        ->orWhere('full_name', 'LIKE', "%$searchText%");
            });
        }

         // Apply the rating filter
    if ($request->has('rating')) {
        $rating = $request->input('rating');
        if($rating < 6){
        $query->whereHas('user_rating', function (Builder $ratingQuery) use ($rating) {
            switch ($rating) {
                case 1:
                    $ratingQuery->where('rating', '>=', 4);
                    break;
                case 2:
                    $ratingQuery->where('rating', '>=', 3);
                    break;
                case 3:
                    $ratingQuery->where('rating', '>=', 2);
                    break;
                case 4:
                    $ratingQuery->where('rating', '>=', 1);
                    break;
                case 5:
                    $ratingQuery->whereNull('rating');
                    break;
            }
        });
     }
    }

        $perPage = 10;
        $providers = $query->paginate($perPage);

        // Format the response data
        $provider_arr = [];
        foreach ($providers as $provider) {
            $temp['daily_help_provider_id'] = $provider->daily_help_provider_id;
            $temp['user_id'] = $provider->user_id;
            $temp['full_name'] = $provider->user->full_name ?? "";
            $temp['mobile_no'] = $provider->user->mobile_no ?? "";
            $temp['profile_pic'] = $provider->user->profile_pic_url ? url($provider->user->profile_pic_url) : "";
            $temp['service_name'] = $provider->daily_help_service->service_name ?? "";
            $temp['service_icon'] = $provider->daily_help_service->service_icon ?? "";
            $temp['rating'] = isset($provider->user_rating)?$provider->user_rating->rating:0;
            $temp['total_reviews'] = isset($provider->user_rating)?$provider->user_rating->total_reviews:0;

            array_push($provider_arr, $temp);
        }

        $data['provider_list'] = $provider_arr;
        $data['total_records'] = $providers->total();

        return $this->sendResponseWithData($data, "All Provider Successfully.");
    }

    public function get_service_provider(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'daily_help_provider_id' => 'required|exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the specified business profile
        $provider = ServiceProvider::with('user','daily_help_service','front_img','back_img','user_rating','work_flat')->find($request->daily_help_provider_id);

        // If profile not found, return error response
        if (!$provider) {
            return response()->json(['error' => 'provider not found'], 404);
        }
        $work_in_flats = [];
        if(isset($provider->work_flat)){

            foreach($provider->work_flat as $work_flat){
                $work_start_time = new DateTime($work_flat->work_start_time);
                $work_end_time = new DateTime($work_flat->work_end_time);
                $flat_info = getSocietyBlockAndFlatInfo($work_flat->block_flat_id);
                $work_temp['block_flat_no'] = $flat_info['block_name'] .'-'. $flat_info['flat_no'];
                $work_temp['work_time'] = $work_start_time->format('g:i A') . " to " . $work_end_time->format('g:i A');
                array_push($work_in_flats, $work_temp);
            }
        }


        $alreadyReviewed = ServiceProviderReview::where('created_by', Auth::id())
            ->where('daily_help_provider_id', $provider->daily_help_provider_id)
            ->exists();

        $data = array();
        $temp['daily_help_provider_id'] = $provider->daily_help_provider_id;
        $temp['user_id'] = $provider->user_id;
        $temp['daily_help_user_passcode'] = isset($provider->user)?$provider->user->user_code:"";
        $temp['full_name'] = isset($provider->user)?$provider->user->full_name:"";
        $temp['mobile_no'] = isset($provider->user)?$provider->user->mobile_no:"";
        $temp['profile_pic'] = isset($provider->user) && $provider->user->profile_pic_url != ""?url($provider->user->profile_pic_url):"";
        $temp['gender'] = isset($provider->user)?$provider->user->gender:"";
        $temp['service_name'] = isset($provider->daily_help_service)?$provider->daily_help_service->service_name:"";
        $temp['service_icon'] = $provider->daily_help_service->service_icon ?? "";
        $temp['rating'] = isset($provider->user_rating)?$provider->user_rating->rating:0;
        $temp['total_reviews'] = isset($provider->user_rating)?$provider->user_rating->total_reviews:0;
        $temp['indentity_proof_front_img'] = isset($provider->front_img) ? url($provider->front_img->file_url):"";
        $temp['indentity_proof_back_img'] = isset($provider->back_img) ?url($provider->back_img->file_url):"";
        $temp['can_add_review'] = !$alreadyReviewed;
        $temp['work_in_flats'] = $work_in_flats;

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All provider Retrieved Successfully.");
    }

    public function delete_service_provider(Request $request)
    {
        $designation_id = $this->payload['designation_id'];
        if($designation_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'daily_help_provider_id' => 'required|exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $isAssociatedWithFlat = ServiceProviderWorkFlat::where('daily_help_provider_id', $request->daily_help_provider_id)->exists();
        if ($isAssociatedWithFlat) {
            return $this->sendError(400, 'Provider is associated with a flat and cannot be deleted.', "Conflict", []);
        }

        // Find the profile to delete
        $provider = ServiceProvider::find($request->daily_help_provider_id);
        if(getResidentDesignation($designation_id) == "Society Member" &&  $provider->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
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
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }

        $society_id = $this->payload['society_id'];
        if($society_id == ""){
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
                'date_format:H:i'
            ],
            'to_time' => [
                'required',
                'date_format:H:i',
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
        $work->created_at = now();
        $work->created_by = Auth::user()->user_id;
        $work->updated_by = Auth::user()->user_id;
        $work->save();

        // if($work){
        //     $visitor = New SocietyVisitor();
        //     $visitor->daily_help_provider_id = $request->daily_help_provider_id;
        //     $visitor->society_id = $society_id;
        //     $visitor->block_flat_id = $block_flat_id;
        //     $visitor->visitor_type = 4;
        //     $visitor->total_visitors = 1;
        //     $visitor->service_vendor_id = 0;
        //     $visitor->visitor_user_id = $provider->user_id;
        //     $visitor->entry_time = $request->to_time;
        //     $visitor->approved_by = 0;
        //     $visitor->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        //     $visitor->created_by = Auth::user()->user_id;
        //     $visitor->updated_by = Auth::user()->user_id;
        //     $visitor->save();
        // }

        $data = array();
        $temp['work_flat_id'] = $work->work_flat_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, 'Service Provider Work Flat added successfully');
    }

    public function service_provider_delete_flat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_flat_id' => 'required||exists:daily_help_provider_work_flat,work_flat_id,deleted_at,NULL',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $work = ServiceProviderWorkFlat::find($request->work_flat_id);
        $work->delete();

        // Return success response
        return $this->sendResponseSuccess("Flat deleted successfully.");
    }

    public function service_provider_add_review(Request $request)
    {
        $block_flat_id = $this->payload['block_flat_id'];
        if($block_flat_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $rules = [
            'daily_help_provider_id' => 'required|exists:daily_help_provider,daily_help_provider_id,deleted_at,NULL',
            'rating' => ['required', Rule::in([1, 2, 3, 4, 5])],
            'review_text' => 'required|string|max:200',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        DB::beginTransaction();

        try {
            // Insert review
            $review = new ServiceProviderReview();
            $review->given_by_bloack_flat_id = $block_flat_id;
            $review->daily_help_provider_id = $request->daily_help_provider_id;
            $review->number_of_star = $request->rating;
            $review->review_text = $request->review_text;
            $review->created_at = now();
            $review->created_by = Auth::user()->user_id;
            $review->updated_by = Auth::user()->user_id;
            $review->save();

            // Update user rating
            $provider = ServiceProvider::find($request->daily_help_provider_id);
            $existingRating = UserRating::where('user_id', $provider->user_id)->first();

            if ($existingRating) {
                $totalReviews = $existingRating->total_reviews + 1;
                $newRating = (($existingRating->rating * $existingRating->total_reviews) + $request->rating) / $totalReviews;

                $existingRating->rating = round($newRating, 2); // Round to 2 decimal places
                $existingRating->total_reviews = $totalReviews;
                $existingRating->updated_by = Auth::user()->user_id;
                $existingRating->save();
            } else {
                $newRating = new UserRating();
                $newRating->user_id = $provider->user_id;
                $newRating->rating = $request->rating;
                $newRating->total_reviews = 1;
                $newRating->estatus = 1;
                $newRating->updated_by = Auth::user()->user_id;
                $newRating->save();
            }

            DB::commit();

            $data = [];
            $temp['review_id'] = $review->daily_help_provider_review_id;
            array_push($data, $temp);
            return $this->sendResponseWithData($data, 'Add Review successfully');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return $this->sendError(500, 'Failed to add review. Please try again.', "Server Error", []);
        }
    }

    public function service_provider_review_list(Request $request)
    {
        $query = ServiceProviderReview::with('user')->where('review_status',1);
        $perPage = 10;
        $reviews = $query->paginate($perPage);
        $total_one_star_rating = $this->getStarRatingCount(1);
        $total_two_star_rating = $this->getStarRatingCount(2);
        $total_three_star_rating = $this->getStarRatingCount(3);
        $total_four_star_rating = $this->getStarRatingCount(4);
        $total_five_star_rating = $this->getStarRatingCount(5);
        $total_reviews = $total_one_star_rating + $total_two_star_rating + $total_three_star_rating + $total_four_star_rating + $total_five_star_rating;

        // Format the response data
        $reviews_arr = [];
        foreach ($reviews as $review) {
            $temp['daily_help_provider_review_id'] = $review->daily_help_provider_review_id;
            $temp['rating'] = $review->number_of_star;
            $temp['review_text'] = $review->review_text;
            $temp['review_by_user_id'] = isset($review->user)?$review->user->user_id:"";
            $temp['review_by_user_fullname'] = isset($review->user)?$review->user->full_name:"";
            $temp['review_by_user_block_flat_no'] = isset($review->user)?getUserBlockAndFlat($review->user->user_id):"";
            $temp['review_by_user_profile_pic'] = isset($review->user) && $review->user->profile_pic_url != ""?url($review->user->profile_pic_url):"";
            $temp['review_time'] =  Carbon::parse($review->created_at)->format('d-m-Y H:i:s');
            $temp['review_time_str'] = Carbon::parse($review->created_at)->diffForHumans();
            array_push($reviews_arr, $temp);
        }

        $data['total_one_star_rating'] = $total_one_star_rating;
        $data['total_two_star_rating'] = $total_two_star_rating;
        $data['total_three_star_rating'] = $total_three_star_rating;
        $data['total_four_star_rating'] = $total_four_star_rating;
        $data['total_five_star_rating'] = $total_five_star_rating;
        $data['total_reviews'] = $total_reviews;
        $data['provider_list'] = $reviews_arr;
        $data['total_records'] = $reviews->toArray()['total'];
        return $this->sendResponseWithData($data, "All Provider Successfully.");
    }

    public function getStarRatingCount(int $starRating): int
    {
        return ServiceProviderReview::where('review_status', 1)
                   ->where('number_of_star', $starRating)
                   ->count();
    }

}
