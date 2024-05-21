<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_announcement(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'announcement_id' => 'required|numeric',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg',
            'title' => 'required|max:100',
            'description' => 'required|max:1000',
            'society_id' => 'required|exists:society',
        ];
        if ($request->has('announcement_id') && $request->input('announcement_id') != 0) {
            $rules['announcement_id'] .= '|exists:announcement,announcement_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->announcement_id == 0){
            $announcement = New Announcement();
            $announcement->society_id = $society_id;
            $announcement->created_at = now();
            $announcement->created_by = Auth::user()->user_id;
            $announcement->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $announcement = Announcement::find($request->announcement_id);
            $announcement->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $announcement->announcement_title = $request->title;
        $announcement->announcement_description = $request->description;
        $announcement->save();

        if($announcement){
            if ($request->hasFile('featured_image')) {
                $existingFile = AnnouncementFile::where('announcement_id', $announcement->announcement_id)->first();
                if ($existingFile) {
                    // Optionally delete the file from storage
                    // Storage::delete($existingFile->file_url);
                    $existingFile->delete();
                }

                $file = $request->file('featured_image');
                $fileUrl = UploadImage($file,'images/announcement');
                $fileType = getFileType($file);
                $doc_file = new AnnouncementFile();
                $doc_file->announcement_id = $announcement->announcement_id;
                $doc_file->file_type = $fileType;
                $doc_file->file_url = $fileUrl;
                $doc_file->uploaded_at = now();
                $doc_file->save();
            }
        }

        $data = array();
        $temp['announcement_id'] = $announcement->announcement_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Announcement ".$action." Successfully");
    }

    public function announcement_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $search_text = $request->input('search_text');

        // Query announcements with related announcement files
        $announcements = Announcement::with('announcement_file','user')
            ->where('society_id', $society_id)
            ->where('estatus', 1);

        // If search text is provided, filter announcements based on it
        if ($search_text) {
            $announcements->where(function ($query) use ($search_text) {
                $query->where('announcement_title', 'like', '%' . $search_text . '%')
                    ->orWhere('announcement_description', 'like', '%' . $search_text . '%');
            });
        }

        $announcements = $announcements->orderBy('created_at', 'DESC')->paginate(10);

        $announcement_arr = array();
        foreach ($announcements as $announcement) {
            //  $block_flat_no = "";
            //  if(isset($announcement->user->societymembers)){
            //        foreach($announcement->user->societymembers as $societymember){
            //             $flat_info = getSocietyBlockAndFlatInfo($societymember['block_flat_id']);
            //             if($block_flat_no == ""){
            //                 $block_flat_no = $flat_info['block_name'] .'-'. $flat_info['flat_no'];
            //             }else{
            //                 $block_flat_no .= ",".$flat_info['block_name'] .'-'. $flat_info['flat_no'];
            //             }
            //        }
            //  };
            $temp['announcement_id'] = $announcement['announcement_id'];
            $temp['title'] = $announcement->announcement_title;
            $temp['description'] = $announcement->announcement_description;
            $temp['full_name'] = $announcement->user->full_name;
            // $temp['block_flat_no'] = $block_flat_no;
            $temp['profile_pic'] = $announcement->user->profile_pic_url;
            $temp['featured_image'] = isset($announcement->announcement_file)?url($announcement->announcement_file->file_url):"";
            $temp['date'] = $announcement->created_at->format('d-m-Y H:i:s');
            array_push($announcement_arr, $temp);
        }

        $data['announement_list'] = $announcement_arr;
        $data['total_records'] = $announcements->toArray()['total'];
        return $this->sendResponseWithData($data, "All Announcement Successfully.");
    }

    public function delete_announcement(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|exists:announcement,announcement_id,deleted_at,NULL,society_id,'.$society_id,
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
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|exists:announcement,announcement_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $announcement = Announcement::with('announcement_file','user')->where('estatus',1)->where('announcement_id',$request->announcement_id)->first();
        if (!$announcement){
            return $this->sendError(404,"You can not get this announcement", "Invalid folder", []);
        }
        // $block_flat_no = "";
        // if(isset($announcement->user->societymembers)){
        //     foreach($announcement->user->societymembers as $societymember){
        //         $flat_info = getSocietyBlockAndFlatInfo($societymember['block_flat_id']);
        //         if($block_flat_no == ""){
        //             $block_flat_no = $flat_info['block_name'] .'-'. $flat_info['flat_no'];
        //         }else{
        //             $block_flat_no .= ",".$flat_info['block_name'] .'-'. $flat_info['flat_no'];
        //         }
        //     }
        // };
        $data = array();
        $temp['announcement_id'] = $announcement['announcement_id'];
        $temp['title'] = $announcement->announcement_title;
        $temp['description'] = $announcement->announcement_description;
        $temp['full_name'] = $announcement->user->full_name;
        // $temp['block_flat_no'] = $block_flat_no;
        $temp['profile_pic'] = $announcement->user->profile_pic_url;
        $temp['featured_image'] = isset($announcement->announcement_file)?url($announcement->announcement_file->file_url):"";
        $temp['date'] = $announcement->created_at->format('d-m-Y H:i:s');
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Announcement Details Successfully.");
    }
}
