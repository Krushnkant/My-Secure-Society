<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
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
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'announcement_id' => 'required',
            'featuredImage' => $request->has('featuredImage') ? 'image|mimes:jpeg,png,jpg' : '',
            'title' => 'required|max:100',
            'description' => 'required|max:1000',
            'society_id' => 'required|exists:society_id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->announcement_id == 0){
            $announcement = New Announcement();
            $announcement->society_id = $society_id;
            $announcement->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $announcement->created_by = Auth::user()->user_id;
            $announcement->updated_by = Auth::user()->user_id;
        }else{
            $announcement = Announcement::find($request->user_id);
            $announcement->updated_by = Auth::user()->user_id;
        }

        $announcement->announcement_title = $request->title;
        $announcement->announcement_description = $request->description;
        $announcement->save();
        return $this->sendResponseSuccess("Announcement Added Successfully");
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
