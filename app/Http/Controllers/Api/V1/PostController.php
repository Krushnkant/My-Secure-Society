<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyPost;
use App\Models\DailyPostFile;
use App\Models\DailyPostLike;
use App\Models\DailyPostPoleOption;

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
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'society_id' => 'required|exists:society',
            'post_id' => 'required|integer',
            'parent_post_id' => 'required|integer',
            'post_type' => 'required|in:1,2,3,4',
            'post_description' => 'required|max:500',
            'bg_color' => 'nullable|max:20',
            'event_time' => 'sometimes|required_if:post_type,3|date',
            'event_venue' => 'required_if:post_type,3',
            'poll_options' => 'required_if:post_type,2'
        ];

        if ($request->parent_post_id != 0) {
            $rules['parent_post_id'] .= '|exists:society_daily_post';
        }

        if ($request->has('poll_options') && $request->post_type == 2) {
            $rules['poll_options'] .= '|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->post_id == 0){
            $post = New DailyPost();
            $post->society_id = $society_id;
            $post->created_at = now();
            $post->created_by = Auth::user()->user_id;
            $post->updated_by = Auth::user()->user_id;
            $post->total_like = 0;
            $post->total_comment = 0;
            $post->total_shared = 0;
            $action = "Added";
        }else{
            $post = DailyPost::find($request->post_id);
            $post->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $post->society_daily_post_id  = $request->post_id;
        $post->parent_post_id = $request->parent_post_id;
        $post->post_type = $request->post_type;
        $post->post_description = $request->post_description;
        $post->bg_color = $request->bg_color;
        $post->event_time = $request->event_time;
        $post->event_venue = $request->event_venue;
        $post->event_time = $request->event_time;
        $post->save();

        if($post){
            if ($request->hasFile('media_files')) {
                $files = $request->file('media_files');
                foreach ($files as $file) {
                    $fileType = getFileType($file);
                    $fileUrl = UploadImage($file,'images/daily_post');
                    $this->storeFileEntry($post->society_daily_post_id, $fileType, $fileUrl);
                }
            }
            if ($request->has('poll_options') && is_array($request->poll_options) && count($request->poll_options) > 0) {
                foreach ($request->poll_options as $optionText) {
                    if($optionText != ""){
                       $this->storePollOption($post->society_daily_post_id, $optionText);
                    }
                }
            }

        }

        return $this->sendResponseSuccess("Post ".$action." Successfully");
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
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $post_type = $request->input('post_type');
        $user_id = $request->input('user_id');
        $post_id = $request->input('post_id');

        $postsQuery = DailyPost::with('user','poll_options')->where('society_id', $society_id)->where('estatus',1);

        if ($post_type !== null) {
            $postsQuery->where('post_type', $post_type);
        }
    
        // Filter by user ID if provided
        if ($user_id !== null) {
            $postsQuery->where('created_by', $user_id);
        }
    
        // Filter by post ID if provided and non-zero
        if ($post_id !== null && $post_id != 0) {
            $post = $postsQuery->find($post_id);
            if (!$post) {
                return $this->sendError(400, 'Post Not Found.', "Not Found", []);
            }
        }
        
        $posts = $postsQuery->orderBy('created_at', 'DESC')->paginate(10);
        
        $post_arr = array();
        foreach ($posts as $post) {
            $temp['post_id'] = $post->society_daily_post_id;
            $temp['post_type'] = $post->post_type;
            $temp['post_description'] = $post->post_description;
            $temp['bg_color'] = $post->bg_color;
            $temp['total_like'] = $post->total_like;
            $temp['total_comment'] = $post->total_comment;
            $temp['total_shared'] = $post->total_shared;
            $temp['event_time'] = $post->event_time;
            $temp['is_like'] = false;
            $temp['user_id'] = $post->created_by;
            $temp['full_name'] = $post->user->full_name;
            $temp['block_flat_no'] = "";
            $temp['profile_pic'] = isset($post->user->profile_pic_url) ? url($post->user->profile_pic_url) : "";
            $temp['post_date'] = $post->created_at->format('d-m-Y H:i:s');
            $temp['poll_options'] = $post->poll_options;
            $temp['option_id'] = "";
            $temp['option_text'] = "";
            $temp['is_voted'] = false;

            array_push($post_arr, $temp);
        }

        $data['posts'] = $post_arr;
        $data['total_records'] = $posts->toArray()['total'];
        return $this->sendResponseWithData($data, "All Post Successfully.");
    }

    public function delete_daily_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $post = DailyPost::find($request->post_id);
        if ($post) {
            $post->estatus = 3;
            $post->save();
            $post->delete();
        }
        return $this->sendResponseSuccess("post deleted Successfully.");
    }

    public function get_daily_post(Request $request)
    {
        $user_id =  Auth::user()->user_id;
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $post = DailyPost::with('user','poll_options')->where('estatus',1)->where('society_daily_post_id',$request->post_id)->first();
        if (!$post){
            return $this->sendError(404,"You can not get this post", "Invalid Post", []);
        }
        $data = array();
        $temp['post_id'] = $post->society_daily_post_id;
        $temp['post_type'] = $post->post_type;
        $temp['post_description'] = $post->post_description;
        $temp['bg_color'] = $post->bg_color;
        $temp['total_like'] = $post->total_like;
        $temp['total_comment'] = $post->total_comment;
        $temp['total_shared'] = $post->total_shared;
        $temp['event_time'] = $post->event_time;
        $temp['is_like'] = false;
        $temp['user_id'] = $post->created_by;
        $temp['full_name'] = $post->user->full_name;
        $temp['block_flat_no'] = "";
        $temp['profile_pic'] = isset($post->user->profile_pic_url) ? url($post->user->profile_pic_url) : "";
        $temp['post_date'] = $post->created_at->format('d-m-Y H:i:s');
        $temp['poll_options'] = $post->poll_options;
        $temp['option_id'] = "";
        $temp['option_text'] = "";
        $temp['is_voted'] = false;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Post Details Successfully.");
    }

    public function update_like(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:society_daily_post,society_daily_post_id', // Assuming your daily posts table is named daily_posts
            'is_like' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Check if a like entry exists for the post by the current user
        $existingLike = DailyPostLike::where('society_daily_post_id', $request->post_id)
                                    ->where('user_id', auth()->id())
                                    ->first();

        // If is_like is false and an entry exists, delete the like entry
        if (!$request->is_like && $existingLike) {
            $existingLike->delete();
        }

        // If is_like is true and no entry exists, create a new like entry
        if ($request->is_like && !$existingLike) {
            DailyPostLike::create([
                'society_daily_post_id' => $request->post_id,
                'user_id' => auth()->id(),
            ]);
        }

        // Update like count in the daily_post table if necessary
        if ($request->is_like) {
            DailyPost::where('society_daily_post_id', $request->post_id)->increment('total_like');
        }

        // Send notification on like (you can implement this part based on your notification system)
        return $this->sendResponseSuccess("Like updated successfully.");

    }

    
}