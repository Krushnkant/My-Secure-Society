<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\ServiceVendor;
use App\Models\DeliveredCourier;
use App\Models\DeliveredCourierFile;
use App\Models\Flat;

class DeliveredCourierController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_courier_delivered_at_gate(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'delivered_at_gate_id' => 'required',
            'block_flat_id' => 'required|exists:block_flat,block_flat_id,deleted_at,NULL',
            'visitor_id' => 'required|exists:society_visitor,society_visitor_id,deleted_at,NULL,society_id,'.$society_id,
            'service_vendor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !ServiceVendor::where('service_vendor_id', $value)->exists()) {
                        $fail("The selected service vendor does not exist.");
                    }
                },
            ],
            'company_name' => 'nullable|string|max:100',
            'total_parcel' => 'required|integer',
            'courier_note' => 'nullable|string|max:100',
            'courier_images' => 'required|array|min:1|max:5',
            'courier_images.*.file' => 'nullable|file|mimetypes:image/jpeg,image|max:20480',
            'courier_images.*.file_id' => [
            'nullable',
            'integer',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !DeliveredCourierFile::where('delivered_courier_file_id', $value)->exists()) {
                        $fail("The selected file ID does not exist.");
                    }
                }
            ],
            'courier_images.*.is_deleted' => 'required|in:1,2',
        ];

        $messages = [
            'courier_images.required_without' => 'Min 1 image is required'
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $blockFlat = Flat::with('society_block')->find($request->block_flat_id);
        if (!$blockFlat || $blockFlat->society_block->society_id != $society_id) {
            return $this->sendError(422, 'The selected Flat is not associated with the provided Society.', "Validation Errors", []);
        }

        if ($request->has('delivered_at_gate_id') && $request->delivered_at_gate_id != 0) {
            // Update existing courier
            $courier = DeliveredCourier::find($request->delivered_at_gate_id);
            if (!$courier) {
                return $this->sendError(404, 'delivered courier not found.', "Not Found", []);
            }
            $courier->updated_by = Auth::user()->user_id;
        } else {
            // Create new courier
            $courier = new DeliveredCourier();
            $courier->created_by = Auth::user()->user_id;
            $courier->updated_by = Auth::user()->user_id;
            $courier->society_id = $this->payload['society_id'];
        }
        $courier->visitor_id = $request->visitor_id;
        $courier->block_flat_id = $request->block_flat_id;
        $courier->service_vendor_id = $request->input('service_vendor_id');
        $courier->company_name = $request->input('company_name');
        $courier->total_parcel = $request->input('total_parcel');
        $courier->courier_note = $request->input('courier_note');
        $courier->collection_otp = mt_rand(100000,999999);
        $courier->courier_collection_status = 1;
        $courier->save();

        if($courier){
            // if ($request->hasFile('courier_images')) {
            //     $files = $request->file('courier_images');
            //     foreach ($files as $file) {
            //         $fileType = getFileType($file);
            //         $fileUrl = UploadImage($file, 'images/delivered_courier');
            //         $this->storeFileEntry($courier->delivered_courier_at_gate_id, $fileType, $fileUrl);
            //     }
            // }

            if ($request->has('courier_images')) {
                foreach ($request->courier_images as $media) {
                    if (isset($media['is_deleted']) && $media['is_deleted'] == 1) {
                        // Delete the file if marked for deletion
                        if (isset($media['file_id'])) {
                            DeliveredCourierFile::where('delivered_courier_file_id', $media['file_id'])->delete();
                        }
                    } elseif (isset($media['file'])) {
                        // Upload new file
                        $file = $media['file'];
                        $fileType = getFileType($file);
                        $fileUrl = UploadImage($file, 'images/delivered_courier');
                        $this->storeFileEntry($courier->delivered_courier_at_gate_id, $fileType, $fileUrl,$media['file_id']);
                    }
                }
            }
        }

        // Prepare the response data
        $data['delivered_at_gate_id'] = $courier->delivered_courier_at_gate_id;

        // Return success response
        return $this->sendResponseWithData($data, "Delivered At Gate saved successfully");
    }

    public function storeFileEntry($Id, $fileType, $fileUrl,$file_id)
    {
        if($file_id > 0){
            $fileEntry = DeliveredCourierFile::find($file_id);
        }else{
            $fileEntry = new DeliveredCourierFile();
        }
        $fileEntry->delivered_courier_at_gate_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now(); // You may need to adjust this based on your timezone settings
        $fileEntry->save();
    }

    public function delivered_at_gate_courier_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'collection_status' => 'required|integer|in:0,1,2',
            'date' => 'nullable|date_format:Y-m-d'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $collection_status = $request->input('collection_status');
        $date = $request->input('date');

        $query = DeliveredCourier::with('service_vendor.service_vendor_file','parcel_image')
            ->where('society_id', $society_id);

        if ($collection_status != 0) {
            $query->where('courier_collection_status', $collection_status);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $gatecourier = $query->orderBy('created_at', 'DESC')->paginate(10);

        $courier_arr = [];
        foreach ($gatecourier as $courier) {
            $temp['delivered_at_gate_id'] = $courier->delivered_courier_at_gate_id;
            if($courier->service_vendor_id > 0){
                $temp['company_name'] = optional($courier->service_vendor)->vendor_company_name ?? '';
                $temp['company_icon'] =   isset($courier->service_vendor->service_vendor_file) ? url($courier->service_vendor->service_vendor_file->file_url) : '';
            }else{
                $temp['company_name'] = $courier->company_name;
                $temp['company_icon'] = '';
            }

            $flat_info = getSocietyBlockAndFlatInfo($courier->block_flat_id);
            $temp['total_parcel'] = $courier->total_parcel;
            $temp['collection_otp'] = $courier->collection_otp;
            $temp['courier_images'] = isset($courier->parcel_image) ? url($courier->parcel_image->file_url) : '';
            $temp['block_flat_no'] =  $flat_info['block_name'] .'-'. $flat_info['flat_no'];
            $temp['collection_status'] = $courier->courier_collection_status;
            array_push($courier_arr, $temp);
        }

        $data['parcel_list'] = $courier_arr;
        $data['total_records'] = $gatecourier->total();

        return $this->sendResponseWithData($data, "Delivered At Gate list retrieved successfully.");

    }

    public function get_courier_delivered_at_gate(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'delivered_at_gate_id' => 'required|integer|exists:delivered_courier_at_gate,delivered_courier_at_gate_id,society_id,' . $society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $courier = DeliveredCourier::with('service_vendor.service_vendor_file','parcel_images')->where('delivered_courier_at_gate_id',$request->delivered_at_gate_id)
        ->where('society_id', $society_id)->first();

        if (!$courier) {
            return $this->sendError(404, "Delivered At Gate not found", "Invalid Gatepass", []);
        }
        $parcel_images = [];
        foreach ($courier->parcel_images as $parcel_image) {
            $file_temp['file_id'] = $parcel_image->delivered_courier_file_id;
            // $file_temp['file_type'] = $parcel_image->file_type;
            $file_temp['file'] = url($parcel_image->file_url);
            array_push($parcel_images, $file_temp);
        }
        $data = array();
        $temp['delivered_at_gate_id'] = $courier->delivered_courier_at_gate_id;
        if($courier->service_vendor_id > 0){
            $temp['company_name'] = optional($courier->service_vendor)->vendor_company_name ?? '';
            $temp['company_icon'] =   isset($courier->service_vendor->service_vendor_file) ? url($courier->service_vendor->service_vendor_file->file_url) : '';
        }else{
            $temp['company_name'] = $courier->company_name;
            $temp['company_icon'] = '';
        }
        $flat_info = getSocietyBlockAndFlatInfo($courier->block_flat_id);
        $temp['total_parcel'] = $courier->total_parcel;
        $temp['collection_otp'] = $courier->collection_otp;
        $temp['courier_images'] = $parcel_images;
        $temp['block_flat_id'] = $courier->block_flat_id;
        $temp['block_flat_no'] =  $flat_info['block_name'] .'-'. $flat_info['flat_no'];
        $temp['society_id'] = $courier->society_id;
        $temp['collection_status'] = $courier->courier_collection_status;
        $temp['courier_note'] = $courier->courier_note;

        array_push($data, $temp);

        return $this->sendResponseWithData($data, "Delivered At Gate retrieved successfully.");
    }

    public function delivered_at_gate_courier_change_status(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'delivered_at_gate_id' => 'required|integer|exists:delivered_courier_at_gate,delivered_courier_at_gate_id,society_id,'.$society_id,
            'collection_status' => 'required|integer|in:1,2',
            'collection_otp' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $courier = DeliveredCourier::where('delivered_courier_at_gate_id',$request->delivered_at_gate_id)->where('collection_otp',$request->collection_otp)->first();
        if (!$courier) {
            return $this->sendError(404, "Delivered At Gate not found", "Invalid Gatepass", []);
        }
        if ($courier) {
            $courier->courier_collection_status = $request->collection_status;
            $courier->collected_time = now();
            $courier->save();
        }
        return $this->sendResponseSuccess("Delivered At Gate status updated successfully.");
    }
}
