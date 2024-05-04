<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenity;
use App\Models\AmenityFile;
use App\Models\AmenitySlot;
use App\Models\AmenityBooking;

class AmenityController extends BaseController
{

    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_amenity(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'is_chargable' => 'required|in:1,2',
            'allowed_payment_type' => 'required|in:1,2,3',
            'max_booking_per_slot' => 'required|integer',
            'max_people_per_booking' => 'required|integer',
            'image_files' => 'required|array|min:1|max:5',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:2048',
            'slot_list' => 'required|array|min:1',
            'slot_list.*.from_time' => 'required|date_format:H:i',
            'slot_list.*.to_time' => 'required|date_format:H:i|after:slot_list.*.from_time',
            'slot_list.*.booking_fee' => 'required|numeric',
            'slot_list.*.is_deleted' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Handle the request data and save the amenity details
        $amenity = new Amenity();
        $amenity->society_id = $request->society_id;
        $amenity->amenity_name = $request->title;
        $amenity->amenity_description = $request->description;
        $amenity->is_chargeable = $request->is_chargable;
        $amenity->applicable_payment_type = $request->allowed_payment_type;
        $amenity->max_booking_per_slot = $request->max_booking_per_slot;
        $amenity->max_people_per_booking = $request->max_people_per_booking;
        $amenity->created_by = Auth::user()->user_id;
        $amenity->updated_by = Auth::user()->user_id;
        $amenity->save();

       
        if ($request->hasFile('image_files')) {
            $files = $request->file('image_files');
            foreach ($files as $file) {
                $fileType = getFileType($file);
                $fileUrl = UploadImage($file,'images/amenity');
                $this->storeFileEntry($amenity->amenity_id, $fileType, $fileUrl);
            }
        }

        // Handle file upload for PDF
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileType = getFileType($file);
            $fileUrl = UploadImage($file,'images/amenity');
            $this->storeFileEntry($amenity->amenity_id, $fileType, $fileUrl);
        }

        // Save slot data
        foreach ($request->slot_list as $slotData) {
            $slot = new AmenitySlot();
            $slot->amenity_id = $amenity->amenity_id;
            $slot->entry_time = $slotData['from_time'];
            $slot->exit_time = $slotData['to_time'];
            $slot->rent_amount = $slotData['booking_fee'];
            $slot->created_by = Auth::user()->user_id;
            $slot->updated_by = Auth::user()->user_id;
            //$slot->is_deleted = $slotData['is_deleted'];
            $slot->save();
        }

        $data = array();
        $temp['amenity_id'] = $amenity->amenity_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Amenity Added Successfully.");
    }

    public function storeFileEntry($Id, $fileType, $fileUrl)
    {
        $fileEntry = new AmenityFile();
        $fileEntry->amenity_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now(); 
        $fileEntry->save();
    }

    public function amenity_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }
        $search_text = $request->input('search_text');
        $AmenitiesQuery = Amenity::with('amenity_images','amenity_pdf')
                                ->where('society_id', $society_id)
                                ->where('estatus', 1);
        
        if ($search_text) {
            $announcements->where(function ($query) use ($search_text) {
                $query->where('amenity_name', 'like', '%' . $search_text . '%')
                    ->orWhere('amenity_description', 'like', '%' . $search_text . '%');
            });
        }
    
        $amenities = $AmenitiesQuery->orderBy('created_at', 'DESC')->paginate(10);
        
        $amenity_arr = [];
        foreach ($amenities as $amenity) {
        
            $temp['amenity_id'] = $amenity->amenity_id;
            $temp['title'] = $amenity->amenity_name;
            $temp['description'] = $amenity->amenity_description;
            $temp['is_chargable'] = $amenity->is_chargeable;
            $temp['allowed_payment_type'] = $amenity->applicable_payment_type;
            $temp['max_booking_per_slot'] = $amenity->max_booking_per_slot;
            $temp['max_people_per_booking'] = $amenity->max_people_per_booking;
            $temp['image_urls'] = $amenity->amenity_images;
            $temp['pdf_url'] = $amenity->amenity_pdf;
          
            array_push($amenity_arr, $temp);
        }
    
        $data['amenity_list'] = $amenity_arr;
        $data['total_records'] = $amenities->toArray()['total'];
        return $this->sendResponseWithData($data, "All Amenity Successfully.");
    }
    
    public function delete_amenity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL',
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
    
    public function get_amenity(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL,society_id'.$society_id,
        ]);
    
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
    
        $amenity = Amenity::with('amenity_images','amenity_pdf','amenity_slots')->where('estatus',1)->where('amenity_id',$request->amenity_id)->first();
    
        if (!$amenity){
            return $this->sendError(404,"You can not get this amenity", "Invalid amenity", []);
        }
    
        $slot_arr = [];
        foreach($amenity->amenity_slots as $slot){
            $slot_temp['slot_id'] = $slot->amenity_slot_id;
            $slot_temp['from_time'] = $slot->entry_time;
            $slot_temp['to_time'] = $slot->exit_time;
            $slot_temp['booking_fee'] = $slot->rent_amount;
            $slot_temp['available_booking'] = "";
            array_push($slot_arr, $slot_temp);
        }
        $data = array();
        $temp['amenity_id'] = $amenity->amenity_id;
        $temp['title'] = $amenity->amenity_name;
        $temp['description'] = $amenity->amenity_description;
        $temp['is_chargable'] = $amenity->is_chargeable;
        $temp['allowed_payment_type'] = $amenity->applicable_payment_type;
        $temp['max_booking_per_slot'] = $amenity->max_booking_per_slot;
        $temp['max_people_per_booking'] = $amenity->max_people_per_booking;
        $temp['image_urls'] = $amenity->amenity_images;
        $temp['pdf_url'] = $amenity->amenity_pdf;
        $temp['slot_list'] = $slot_arr;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Amenity Details Successfully.");
    }

    public function create_amenity_booking(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);
        
        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|integer',
            'slot_id' => 'required|integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'required|numeric',
            // 'booking_status' => 'required|integer|in:1,2,3',
            // 'payment_status' => 'required|integer|in:1,2,3,4,5',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the request data and save the amenity details
        $amenity = new AmenityBooking();
        $amenity->user_id = Auth::id();
        $amenity->amenity_id = $request->amenity_id;
        $amenity->amenity_slot_id = $request->slot_id;
        $amenity->start_date = $request->start_date;
        $amenity->end_date = $request->end_date;
        $amenity->total_amount = $request->total_amount;
        $amenity->created_by = Auth::user()->user_id;
        $amenity->updated_by = Auth::user()->user_id;
        $amenity->save();


        $data = array();
        $temp['amenity_booking_id'] = $amenity->amenity_booking_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Amenity Booking Successfully.");
    }
    
      
}
