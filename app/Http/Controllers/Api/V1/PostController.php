<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyPost;
use App\Models\DailyPostFile;
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
            $post->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $post->created_by = Auth::user()->user_id;
            $post->updated_by = Auth::user()->user_id;
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
        $post->total_like = 0;
        $post->total_comment = 0;
        $post->total_shared = 0;
        $post->save();

        if($post){
           //dd($request->hasFile('media_files'));
            if ($request->hasFile('media_files')) {
                $files = $request->file('media_files');

                foreach ($files as $file) {
                    $fileType = $this->getFileType($file);
                    $fileUrl = $this->uploadFile($file);

                    $this->storeFileEntry($post->society_daily_post_id, $fileType, $fileUrl);
                }
            }

            // Poll Options Handling
            if ($request->has('poll_options') && is_array($request->poll_options)) {
                foreach ($request->poll_options as $optionText) {
                    // Create a new entry in the daily_post_pole_option table
                    $this->storePollOption($post->society_daily_post_id, $optionText);
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

    // Method to determine file type based on extension
    public function getFileType($file)
{
    $extension = strtolower($file->getClientOriginalExtension());
    $fileTypes = [
        'jpg' => 1, 'jpeg' => 1, 'png' => 1, 'gif' => 1, // Image
        'pdf' => 4, // PDF
        'mp4' => 2, 'mov' => 2, 'avi' => 2, 'wmv' => 2, 'mkv' => 2 // Video
        // You can add more file types as needed
    ];

    // Check if the extension exists in the $fileTypes array
    if (array_key_exists($extension, $fileTypes)) {
        return $fileTypes[$extension];
    }

    // Default to "Other" if the extension is not recognized
    return 5;
}

    // Method to handle file upload
    public function uploadFile($file)
    {
        $fileName = 'daily_post_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('images/daily_post'); // Define your upload directory
        $file->move($destinationPath, $fileName);
        return 'images/daily_post/' . $fileName; // Return the file URL
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

    public function uploadProfileImage($request,$old_image=""){
        $image = $request->file('profile_pic');
        $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('images/profile_pic');
        $image->move($destinationPath, $image_name);
        if(isset($old_image) && $old_image != "") {
            $old_image = public_path($old_image);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        return  'images/profile_pic/'.$image_name;
    }

    public function announcement_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
        $announcements = Announcement::where('society_id', $society_id)->where('estatus',1);
        $announcements = $announcements->orderBy('created_at', 'DESC')->paginate(10);

        $announcement_arr = array();
        foreach ($announcements as $announcement) {
            $temp['announcement_id'] = $announcement['announcement_id'];
            $temp['announcement_title'] = $announcement->title;
            $temp['announcement_description'] = $announcement->description;
            $temp['date'] = $announcement->created_at;
            array_push($announcement_arr, $temp);
        }

        $data['family_members'] = $announcement_arr;
        $data['total_records'] = $announcements->toArray()['total'];
        return $this->sendResponseWithData($data, "All Announcement Successfully.");
    }

    public function delete_announcement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|exists:announcement',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $announcement = Announcement::find($request->announcement_id);
        if ($announcement) {
            $announcement->estatus = 3;
            $announcement->save();
            $announcement->delete();
        }
        return $this->sendResponseSuccess("announcement deleted Successfully.");
    }

    public function get_announcement(Request $request)
    {
        $user_id =  Auth::user()->user_id;
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|exists:announcement',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $announcement = Announcement::where('estatus',1)->where('announcement_id',$request->announcement_id)->first();
        if (!$announcement){
            return $this->sendError(404,"You can not delete this folder", "Invalid folder", []);
        }
        $data = array();
        $temp['announcement_id'] = $announcement['announcement_id'];
        $temp['announcement_title'] = $announcement->title;
        $temp['announcement_description'] = $announcement->description;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Announcement Details Successfully.");
    }
}
