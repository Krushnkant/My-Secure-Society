<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDescription;
use App\Models\ServiceRequestFile;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceRequestController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_service_request(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'category_id' => 'required|exists:service_category,service_category_id,deleted_at,NULL,society_id,'.$society_id,
            'subject' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:10240',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $flat_info = getSocietyBlockAndFlatInfo($this->payload['block_flat_id']);

        $service = new ServiceRequest();
        $service->created_by = Auth::user()->user_id;
        $service->updated_by = Auth::user()->user_id;
        $service->society_id = $this->payload['society_id'];
        $service->society_block_id = $flat_info['block_id'];
        $service->assigned_to_staff_member_id = 0;
        $service->service_category_id = $request->input('category_id');
        $service->service_subject = $request->input('subject');
        $service->service_request_status = 2;
        $service->service_request_number = generateServiceRequestNumber($society_id);
        $service->save();

        if($service){
           $desc =  new ServiceRequestDescription();
           $desc->created_by = Auth::user()->user_id;
           $desc->service_request_id =  $service->service_request_id;
           $desc->description =  $request->input('description');
           $desc->created_at = now();
           $desc->save();
            if($desc){
                if ($request->hasFile('images')) {
                    $files = $request->file('images');
                    foreach ($files as $file) {
                        $fileType = getFileType($file);
                        $fileUrl = UploadImage($file, 'images/service_request');
                        $this->storeFileEntry($desc->service_req_desc_id, $fileType, $fileUrl);
                    }
                }

                if ($request->hasFile('video')) {
                    $file = $request->file('video');
                    $fileType = getFileType($file);
                    $fileUrl = UploadImage($file,'images/service_request');
                    $this->storeFileEntry($desc->service_req_desc_id, $fileType, $fileUrl);
            }
           }
        }

        // Prepare the response data
        $data['service_request_id'] = $service->service_request_id;

        // Return success response
        return $this->sendResponseWithData($data, "service request saved successfully");
    }

    public function storeFileEntry($Id, $fileType, $fileUrl)
    {
        $fileEntry = new ServiceRequestFile();
        $fileEntry->service_req_desc_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now();
        $fileEntry->save();
    }



    public function service_request_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'request_by_user_id' => 'required|integer',
            'assigned_staffmember_id' => 'required|integer',
            'status' => 'required|integer',
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $query = ServiceRequest::where('society_id', $society_id);

        if ($request->input('request_by_user_id') != 0) {
            $query->where('created_by', $request->input('request_by_user_id'));
        }

        if ($request->input('assigned_staffmember_id') != 0) {
            $query->where('assigned_to_staff_member_id', $request->input('assigned_staffmember_id'));
        }

        if ($request->input('status') != 0) {
            $query->where('service_request_status', $request->input('status'));
        }

        if ($request->input('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->input('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $serviceRequests = $query->orderBy('created_at', 'DESC')->paginate(10);

        $service_arr = [];
        foreach ($serviceRequests as $serviceRequest) {
            $temp = [];
            $temp['service_request_id'] = $serviceRequest->service_request_id;
            $temp['category'] = $serviceRequest->category->service_category_name ?? ''; // Assuming there's a relation 'category'
            $temp['subject'] = $serviceRequest->service_subject;
            $temp['images'] = $serviceRequest->description->images->map(function($image) {
                return url($image->file_url);
            })->toArray();
            $temp['video'] = $serviceRequest->description->video ? url($serviceRequest->description->video->file_url) : '';
            $temp['description'] = $serviceRequest->description->description ?? '';
            $temp['total_reply'] = $serviceRequest->replies ? $serviceRequest->replies->count():0;
            $temp['request_status'] = $serviceRequest->service_request_status;
            $temp['request_status_name'] = getStatusName($serviceRequest->service_request_status);
            $temp['created_by_user_id'] = $serviceRequest->created_by;
            $temp['created_by_user_full_name'] = $serviceRequest->createdBy->full_name ?? ''; // Assuming there's a relation 'createdBy'
            $temp['profile_pic'] = $serviceRequest->createdBy->profile_pic ? url($serviceRequest->createdBy->profile_pic) : '';
            $temp['request_date'] = $serviceRequest->created_at->format('d-m-Y H:i:s');
            $temp['requested_time_str'] = Carbon::parse($serviceRequest->created_at)->diffForHumans();
            array_push($service_arr, $temp);
        }

        $data['service_request_list'] = $service_arr;
        $data['total_records'] = $serviceRequests->total();

        return $this->sendResponseWithData($data, "Service request list retrieved successfully.");
    }

    public function get_service_request(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'service_request_id' => 'required|integer|exists:service_request,service_request_id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $serviceRequest = ServiceRequest::where('society_id', $society_id)
                                        ->where('service_request_id', $request->input('service_request_id'))
                                        ->first();

        if (!$serviceRequest) {
            return $this->sendError(404, 'Service Request Not Found.', "Not Found", []);
        }
        $service_arr = [];

        $data = [
            'service_request_id' => $serviceRequest->service_request_id,
            'category_id' => $serviceRequest->service_category_id,
            'subject' => $serviceRequest->service_subject,
            'total_reply' =>  $serviceRequest->replies ? $serviceRequest->replies->count():0,
            'request_status' => $serviceRequest->service_request_status,
            'request_status_name' => getStatusName($serviceRequest->service_request_status),
            'created_by_user_id' => $serviceRequest->created_by,
            'created_by_user_full_name' => $serviceRequest->createdBy->full_name ?? '',
            'profile_pic' => $serviceRequest->createdBy->profile_pic ? url($serviceRequest->createdBy->profile_pic) : '',
            'created_date' => $serviceRequest->created_at->format('d-m-Y H:i:s'),
            'created_time_str' => Carbon::parse($serviceRequest->created_at)->diffForHumans()
        ];

        array_push($service_arr, $data);

        return $this->sendResponseWithData($service_arr, "Service request retrieved successfully.");
    }

    public function save_service_request_reply(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'service_request_id' => 'required|exists:service_request,service_request_id,deleted_at,NULL,society_id,'.$society_id,
            'assign_to_staffmember_id' => 'required|exists:society_staff_member,society_staff_member_id,deleted_at,NULL',
            'reply_text' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4|max:2048',
            'request_status' => 'required|in:1,2',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $service = ServiceRequest::find($request->service_request_id);
        $service->service_request_status = $request->request_status;
        if($request->assign_to_staffmember_id > 0){
          $service->assigned_to_staff_member_id = $request->assign_to_staffmember_id;
        }
        $service->save();

        $desc = new ServiceRequestDescription();
        $desc->created_by = Auth::user()->user_id;
        $desc->created_at = now();
        $desc->service_request_id = $request->input('service_request_id');
        $desc->description = $request->input('reply_text');
        $desc->save();

        if($desc){
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $file) {
                    $fileType = getFileType($file);
                    $fileUrl = UploadImage($file, 'images/service_request');
                    $this->storeFileEntry($desc->service_req_desc_id, $fileType, $fileUrl);
                }
            }

            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileType = getFileType($file);
                $fileUrl = UploadImage($file,'images/service_request');
                $this->storeFileEntry($desc->service_req_desc_id, $fileType, $fileUrl);
           }
        }

        $data['service_req_desc_id'] = $desc->service_req_desc_id;

        // Return success response
        return $this->sendResponseWithData($data, "service request reply saved successfully");
    }

    public function service_request_reply_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
           'service_request_id' => 'required|exists:service_request,service_request_id,deleted_at,NULL,society_id,'.$society_id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $query = ServiceRequestDescription::with('images','video');
        $query->where('service_request_id', $request->input('service_request_id'));
        $serviceRequests = $query->orderBy('created_at', 'DESC')->paginate(10);

        $service_arr = [];
        foreach ($serviceRequests as $serviceRequest) {
            $temp = [];
            $temp['service_req_desc_id'] = $serviceRequest->service_req_desc_id;
            $temp['service_request_id'] = $serviceRequest->service_request_id;
            $temp['reply_text'] = $serviceRequest->description;
            $temp['images'] = $serviceRequest->images->map(function($image) {
                return url($image->file_url);
            })->toArray();
            $temp['video'] = $serviceRequest->video ? url($serviceRequest->video->file_url) : '';
            $temp['created_by_user_id'] = $serviceRequest->created_by;
            $temp['created_by_user_full_name'] = $serviceRequest->createdBy->full_name ?? ''; // Assuming there's a relation 'createdBy'
            $temp['profile_pic'] = $serviceRequest->createdBy->profile_pic ? url($serviceRequest->createdBy->profile_pic) : '';
            $temp['reply_date'] = $serviceRequest->created_at;
            $temp['reply_time_str'] = Carbon::parse($serviceRequest->created_at)->diffForHumans();
            array_push($service_arr, $temp);
        }

        $data['service_request_reply_list'] = $service_arr;
        $data['total_records'] = $serviceRequests->total();

        return $this->sendResponseWithData($data, "Service request reply list retrieved successfully.");
    }
}
