<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyPost;
use App\Models\DailyPostFile;
use App\Models\DailyPostLike;
use App\Models\DailyPostPoleOption;
use App\Models\ResidentDesignation;
use App\Models\PostReportOption;

class PostController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_daily_post(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id' => $society_id]);

        Validator::extend('min_options_for_poll', function ($attribute, $value, $parameters, $validator) {
            $minOptions = $parameters[0] ?? 2; // Default minimum options required for a poll
            return count($value) >= $minOptions;
        });

        $rules = [
            'society_id' => 'required|exists:society',
            'post_id' => 'required|integer',
            'parent_post_id' => 'required_if:post_type,1,2,3|integer|in:0',
            'parent_post_id' => 'required|integer',
            'post_type' => 'required|in:1,2,3,4',
            'post_description' => 'required|max:500',
            'bg_color' => 'nullable|max:20',
            'event_time' => 'sometimes|required_if:post_type,3|date',
            'event_venue' => 'required_if:post_type,3',
            'poll_options' => 'required_if:post_type,2',
            'media_files.*' => 'nullable|file|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime|max:20480',
        ];

        $messages = [
            'event_venue.required_if' => 'The event venue field is required.',
            'poll_options.required_if' => 'The poll options field is required.'
        ];

        if ($request->parent_post_id != 0) {
            $rules['parent_post_id'] .= '|exists:society_daily_post,society_daily_post_id,deleted_at,NULL';
        }

        if ($request->post_id != 0) {
            $rules['post_id'] .= '|exists:society_daily_post,society_daily_post_id,deleted_at,NULL';
        }

        if ($request->has('poll_options') && $request->post_type == 2) {
            $rules['poll_options'] .= '|array|min_options_for_poll:2';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        if ($request->post_type != 4) {
            if (empty($request->file('media_files')) && empty($request->bg_color)) {
                return $this->sendError(422, 'Background color is required when media files are empty.', "Validation Error", []);
            }
        }

        if ($request->post_id == 0) {
            $post = new DailyPost();
            $post->society_id = $society_id;
            $post->created_at = now();
            $post->created_by = Auth::user()->user_id;
            $post->updated_by = Auth::user()->user_id;
            $post->total_like = 0;
            $post->total_comment = 0;
            $post->total_shared = 0;
            $action = "Added";
        } else {
            $post = DailyPost::find($request->post_id);
            $post->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $post->society_daily_post_id  = $request->post_id;
        $post->parent_post_id = $request->parent_post_id;
        $post->post_type = $request->post_type;
        $post->post_description = $request->post_description;
        if ($request->post_type != 4) {
            $post->bg_color = $request->bg_color;
        }

        if ($request->post_type == 3) {
            $post->event_time = $request->event_time;
            $post->event_venue = $request->event_venue;
        }
        $post->save();

        if ($post) {
            if ($request->hasFile('media_files')) {
                $files = $request->file('media_files');
                foreach ($files as $file) {
                    $fileType = getFileType($file);
                    $fileUrl = UploadImage($file, 'images/daily_post');
                    $this->storeFileEntry($post->society_daily_post_id, $fileType, $fileUrl);
                }
            }
            if ($request->post_type == 2) {
                if ($request->has('poll_options') && is_array($request->poll_options) && count($request->poll_options) > 0) {
                    foreach ($request->poll_options as $optionText) {
                        if ($optionText != "") {
                            $this->storePollOption($post->society_daily_post_id, $optionText);
                        }
                    }
                }
            }
        }


        $data = array();
        $temp['post_id'] = $post->society_daily_post_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Post " . $action . " Successfully");

        return $this->sendResponseSuccess("Post " . $action . " Successfully");
    }

    public function storePollOption($postId, $optionText)
    {
        $pollOption = new DailyPostPoleOption();
        $pollOption->society_daily_post_id = $postId;
        $pollOption->option_text = $optionText;
        $pollOption->total_vote = 0; // Initialize total vote count
        $pollOption->save();
    }

    // Method to store file entry in the database
    public function storeFileEntry($postId, $fileType, $fileUrl)
    {
        $fileEntry = new DailyPostFile();
        $fileEntry->society_daily_post_id = $postId;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now(); // You may need to adjust this based on your timezone settings
        $fileEntry->save();
    }

    public function daily_post_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }
        $designation_id = $this->payload['designation_id'];

        $rules = [
            'user_id' => 'required',
            'post_id' => 'required',
            'post_type' => 'required|in:0,1,2,3,4',
        ];

        if ($request->post_id != 0) {
            $rules['post_id'] .= '|exists:society_daily_post,society_daily_post_id,deleted_at,NULL';
        }

        if ($request->user_id != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $post_type = $request->input('post_type');
        $user_id = $request->input('user_id');
        $post_id = $request->input('post_id');

        $postsQuery = DailyPost::with('user.societymembers', 'poll_options','daily_post_files')
            ->where('society_id', $society_id)
            ->where('estatus', 1);

        if ($post_type !== null && $post_type != 0) {
            $postsQuery->where('post_type', $post_type);
        }else{
            $postsQuery->whereIn('post_type', [1,2,3]);
        }

        if ($user_id !== null && $user_id != 0) {
            $postsQuery->where('created_by', $user_id);
        }
        if(getReasonTypeName($designation_id) == "Society Member"){
            if(auth()->id() != $user_id){
                $postsQuery->where('estatus',"!=",5);
            }
        }


        if ($post_id !== null && $post_id != 0) {
            $postsQuery->where('parent_post_id',$post_id);
        }

        $posts = $postsQuery->orderBy('created_at', 'DESC')->paginate(10);

        $post_arr = [];
        foreach ($posts as $post) {

            $option_arr = [];
            foreach ($post->poll_options as $option) {
                $option_temp['option_id'] = $option->daily_post_pole_option_id;
                $option_temp['option_text'] = $option->option_text;
                $option_temp['is_voted'] = $option->isVoted();
                array_push($option_arr, $option_temp);
            }

            $media_files = [];
            foreach ($post->daily_post_files as $post_file) {
                $file_temp['daily_post_file_id'] = $post_file->daily_post_file_id;
                $file_temp['file_type'] = $post_file->file_type;
                $file_temp['file_url'] = url($post_file->file_url);
                array_push($media_files, $file_temp);
            }

            $temp['post_id'] = $post->society_daily_post_id;
            $temp['post_type'] = $post->post_type;
            $temp['post_description'] = $post->post_description;
            $temp['bg_color'] = $post->bg_color;
            $temp['media_files'] = $media_files;
            $temp['total_like'] = $post->total_like;
            $temp['total_comment'] = $post->total_comment;
            $temp['total_shared'] = $post->total_shared;
            $temp['event_time'] = $post->event_time;
            $temp['is_like'] = $post->isLike();
            $temp['user_id'] = $post->created_by;
            $temp['full_name'] = $post->user->full_name;
            $temp['block_flat_no'] = getUserBlockAndFlat($post->created_by);
            $temp['profile_pic'] = isset($post->user->profile_pic_url) ? url($post->user->profile_pic_url) : "";
            $temp['post_date'] = $post->created_at->format('d-m-Y H:i:s');
            $temp['poll_options'] = $option_arr;

            array_push($post_arr, $temp);
        }

        $data['posts'] = $post_arr;
        $data['total_records'] = $posts->toArray()['total'];
        return $this->sendResponseWithData($data, "All Post Successfully.");
    }

    public function get_daily_post(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $post = DailyPost::with('user', 'poll_options','daily_post_files')->where('estatus', 1)->where('society_daily_post_id', $request->post_id)->first();
        if (!$post) {
            return $this->sendError(404, "You can not get this post", "Invalid Post", []);
        }

        $data = [];
        $option_arr = [];

        foreach ($post->poll_options as $option) {
            $option_temp['option_id'] = $option->daily_post_pole_option_id;
            $option_temp['option_text'] = $option->option_text;
            $option_temp['is_voted'] = $option->isVoted();
            array_push($option_arr, $option_temp);
        }

        $media_files = [];
        foreach ($post->daily_post_files as $post_file) {
            $file_temp['daily_post_file_id'] = $post_file->daily_post_file_id;
            $file_temp['file_type'] = $post_file->file_type;
            $file_temp['file_url'] = url($post_file->file_url);
            array_push($media_files, $file_temp);
        }

        $temp['post_id'] = $post->society_daily_post_id;
        $temp['post_type'] = $post->post_type;
        $temp['post_description'] = $post->post_description;
        $temp['bg_color'] = $post->bg_color;
        $temp['media_files'] = $media_files;
        $temp['total_like'] = $post->total_like;
        $temp['total_comment'] = $post->total_comment;
        $temp['total_shared'] = $post->total_shared;
        $temp['event_time'] = $post->event_time;
        $temp['is_like'] = $post->isLike();
        $temp['user_id'] = $post->created_by;
        $temp['full_name'] = $post->user->full_name;
        $temp['block_flat_no'] = getUserBlockAndFlat($post->created_by);
        $temp['profile_pic'] = isset($post->user->profile_pic_url) ? url($post->user->profile_pic_url) : "";
        $temp['post_date'] = $post->created_at->format('d-m-Y H:i:s');
        $temp['poll_options'] = $option_arr;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Post Details Successfully.");
    }

    public function change_status_daily_post(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $designation_id = $this->payload['designation_id'];
        if($designation_id == ""){
            return $this->sendError(400,'designation Not Found.', "Not Found", []);
        }

        $rules = [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id,deleted_at,NULL,society_id,'.$society_id,
            'status' => 'required|in:1,3,5',
        ];

        if ($request->status == 5) {
            $rules['report_id'] = 'required|exists:daily_post_report_option,daily_post_report_option_id';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $post = DailyPost::find($request->post_id);
        $resident_designation = ResidentDesignation::find($designation_id);
        if($resident_designation->designation_name != "Society Admin"){
            if(($request->status == 3 || $request->status == 1) && $post->estatus == 5  &&  $post->created_by == auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }

            if($resident_designation->designation_name == "Society Member"){
                if($request->status == 3 &&  $post->created_by != auth()->id()){
                    return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
                }
            }
        }

        if ($post) {
            $post->estatus = $request->status;
            if($request->status == 5){
                $post->daily_post_report_option_id = $request->report_id;
            }
            $post->save();
            if($request->status == 3){
                $post->delete();
            }
        }
        return $this->sendResponseSuccess("post status updated Successfully.");
    }

    public function update_like(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id,deleted_at,NULL,society_id,'.$society_id,
            'is_like' => 'required|in:1,2',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }
        $post = DailyPost::where('society_daily_post_id', $request->post_id)->where('estatus','<>', 5)->first();
        if($post){
            $is_like  = $request->is_like;

            $existingLike = DailyPostLike::where('society_daily_post_id', $request->post_id)
                ->where('user_id', auth()->id())
                ->first();

            if (($is_like == 2) && $existingLike) {
                $existingLike->delete();
                DailyPost::where('society_daily_post_id', $request->post_id)->decrement('total_like');
            }

            if (($is_like == 1) && !$existingLike) {
                DailyPostLike::create([
                    'society_daily_post_id' => $request->post_id,
                    'user_id' => auth()->id(),
                ]);
                DailyPost::where('society_daily_post_id', $request->post_id)->increment('total_like');
            }
        }

        return $this->sendResponseSuccess("Like updated successfully.");
    }

    public function report_reason_list()
    {
        $options = PostReportOption::where('estatus', 1)->orderBy('title', 'asc')->get();
        $option_arr = [];
        foreach ($options as $option) {
            $temp['report_option_id'] = $option->daily_post_report_option_id;
            $temp['report_option_text'] = $option->report_option_text;
            array_push($option_arr, $temp);
        }
        $data['option_list'] = $option_arr;
        return $this->sendResponseWithData($data, "All Report Options Successfully.");
    }
}
