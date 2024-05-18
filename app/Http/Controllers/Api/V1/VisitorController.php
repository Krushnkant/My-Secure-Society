<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\ServiceVendor;
use App\Models\VisitorGatepass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class VisitorController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }


    public function service_vendor_list(Request $request)
    {
        $rules = [
            'vendor_type' => 'required|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Initialize the query with the base condition
        $query = ServiceVendor::with('service_vendor_file')->where('estatus', 1);

        // Check if vendor_type is present in the request and is valid
        if ($request->has('vendor_type') && in_array($request->vendor_type, [1, 2, 3])) {
            $query->where('service_type', $request->vendor_type);
        }

        // Execute the query and get the results ordered by vendor_company_name
        $venders = $query->orderBy('vendor_company_name', 'asc')->get();
        $venders_arr = array();
        foreach ($venders as $vender) {
            $temp['service_vendor_id'] = $vender->service_vendor_id;
            $temp['company_name'] = $vender->vendor_company_name;
            $temp['company_icon'] = url($vender->service_vendor_file->file_url);
            array_push($venders_arr, $temp);
        }
        $data['vendor_list'] = $venders_arr;
        return $this->sendResponseWithData($data, "All Service vendor Retrieved Successfully.");
    }

    public function save_gatepass(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'gatepass_id' => 'required',
            'visitor_type' => 'required|integer|in:1,2,3,4',
            'service_vendor_id' => [
                'required_if:visitor_type,1,3,4',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !ServiceVendor::where('service_vendor_id', $value)->exists()) {
                        $fail("The selected service vendor does not exist.");
                    }
                },
            ],
            'daily_help_provider_id' => [
                'required_if:visitor_type,4',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !ServiceProvider::where('daily_help_provider_id', $value)->exists()) {
                        $fail("The selected daily help provider does not exist.");
                    }
                },
            ],
            'company_name' => 'nullable|string|max:100',
            'vehicle_number' => 'nullable|string|max:10|required_if:visitor_type,2',
            'invitation_message' => 'nullable|string|max:300',
            'total_visitors' => 'required|integer|min:1',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d|after_or_equal:from_date',
            'allowed_days' => 'required|array',
            'allowed_days.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'from_time' => 'required|date_format:H:i',
            'to_time' => 'required|date_format:H:i|after:from_time',
            'visitor_name' => 'required|string|max:100',
            'visitor_mobile_no' => 'required|string|digits:10',
        ];

        $msg = [
            'allowed_days.required' => 'Allowed days are required.',
            'allowed_days.array' => 'Allowed days must be an array.',
            'allowed_days.*.in' => 'Allowed days must be one of the following: Mon, Tue, Wed, Thu, Fri, Sat, Sun.',
        ];

        // if ($request->gatepass_id != 0) {
        //     $rules['gatepass_id'] .= '|exists:society_daily_post,society_daily_post_id,deleted_at,NULL';
        // }

        $validator = Validator::make($request->all(), $rules,$msg);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        if ($request->has('gatepass_id') && $request->gatepass_id != 0) {
            // Update existing gatepass
            $gatepass = VisitorGatepass::find($request->gatepass_id);
            if (!$gatepass) {
                return $this->sendError(404, 'Gatepass not found.', "Not Found", []);
            }
            $gatepass->updated_by = Auth::user()->user_id;
        } else {
            // Create new gatepass
            $gatepass = new VisitorGatepass();
            $gatepass->created_by = Auth::user()->user_id;
            $gatepass->updated_by = Auth::user()->user_id;
            $gatepass->society_id = $this->payload['society_id'];
            $gatepass->block_flat_id = $this->payload['block_flat_id'];
        }
        $gatepass->visitor_type = $request->input('visitor_type');
        $gatepass->service_vendor_id = $request->input('service_vendor_id');
        $gatepass->daily_help_provider_id = $request->input('daily_help_provider_id');
        $gatepass->company_name = $request->input('company_name');
        $gatepass->visitor_name = $request->input('visitor_name');
        $gatepass->visitor_mobile_no = $request->input('visitor_mobile_no');
        $gatepass->invitation_message = $request->input('invitation_message');
        $gatepass->total_visitors = $request->input('total_visitors');
        $gatepass->valid_from_date = $request->input('from_date');
        $gatepass->valid_to_date = $request->input('to_date');
        $gatepass->valid_from_time = $request->input('from_time');
        $gatepass->valid_to_time = $request->input('to_time');
        $gatepass->visit_code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $gatepass->allowed_days =implode(',',$request->input('allowed_days'));
        $gatepass->save();

        // Prepare the response data
        $data['gatepass_id'] = $gatepass->visitor_gatepass_id;

        // Return success response
        return $this->sendResponseWithData($data, "Gatepass saved successfully");
    }

    public function gatepass_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'visitor_type' => 'required|integer|in:0,1,2,3,4',
            'date' => 'nullable|date_format:Y-m-d'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $visitor_type = $request->input('visitor_type');
        $date = $request->input('date');
        $page_no = $request->input('page_no', 1);

        $gatepassQuery = VisitorGatepass::with('user','daily_help_provider','service_vendor.service_vendor_file','visitor_image')
            ->where('society_id', $society_id);

        if ($visitor_type != 0) {
            $gatepassQuery->where('visitor_type', $visitor_type);
        }

        if ($date) {
            $gatepassQuery->whereDate('created_at', $date);
        }

        $gatepasses = $gatepassQuery->orderBy('created_at', 'DESC')->paginate(10, ['*'], 'page', $page_no);

        $gatepass_arr = [];
        foreach ($gatepasses as $gatepass) {
            $temp['gatepass_id'] = $gatepass->visitor_gatepass_id;
            $temp['visitor_type'] = $gatepass->visitor_type;
            $temp['daily_help_service_name'] = optional($gatepass->daily_help_provider)->service_name ?? '';
            if($gatepass->service_vendor_id > 0){
                $temp['company_name'] = optional($gatepass->service_vendor)->vendor_company_name ?? '';
                $temp['company_icon'] =   isset($gatepass->service_vendor->service_vendor_file) ? url($gatepass->service_vendor->service_vendor_file->file_url) : '';
            }else{
                $temp['company_name'] = $gatepass->company_name;
                $temp['company_icon'] = '';
            }

            $temp['visitor_name'] = $gatepass->visitor_name;
            $temp['visitor_mobile_no'] = $gatepass->visitor_mobile_no;
            $temp['visitor_image'] = isset($gatepass->visitor_image) ? url($gatepass->visitor_image->file_url) : '';
            $temp['invitation_message'] = $gatepass->invitation_message;
            $temp['total_visitors'] = $gatepass->total_visitors;
            $temp['from_date'] = Carbon::parse($gatepass->valid_from_date)->format('d-m-Y');
            $temp['to_date'] = Carbon::parse($gatepass->valid_to_date)->format('d-m-Y');
            $temp['allowed_days'] = explode(',', $gatepass->allowed_days);
            $temp['from_time'] = Carbon::parse($gatepass->valid_from_time)->format('H:i');
            $temp['to_time'] = Carbon::parse($gatepass->valid_to_time)->format('H:i');
            $temp['visit_code'] = $gatepass->visit_code;
            $temp['added_by_user_id'] = $gatepass->created_by;
            $temp['added_by_user_full_name'] = $gatepass->user->full_name;
            $temp['visit_to_block_flat_no'] = getUserBlockAndFlat($gatepass->created_by);

            array_push($gatepass_arr, $temp);
        }

        $data['gatepass_list'] = $gatepass_arr;
        $data['total_gatepass'] = $gatepasses->total();

        return $this->sendResponseWithData($data, "Gatepass list retrieved successfully.");

    }

    public function get_gatepass(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'gatepass_id' => 'nullable|integer|required_without:visit_code|exists:visitor_gatepass,visitor_gatepass_id,society_id,' . $society_id,
            'visit_code' => 'nullable|integer|required_without:gatepass_id|exists:visitor_gatepass,visit_code,society_id,' . $society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $gatepassQuery = VisitorGatepass::with('user','daily_help_provider','service_vendor.service_vendor_file','visitor_image')
        ->where('society_id', $society_id);

    if ($request->has('gatepass_id') && $request->gatepass_id != "") {
        $gatepassQuery->where('visitor_gatepass_id', $request->gatepass_id);
    }

    if ($request->has('visit_code') && $request->visit_code != "") {
        $gatepassQuery->where('visit_code', $request->visit_code);
    }

    $gatepass = $gatepassQuery->first();

        if (!$gatepass) {
            return $this->sendError(404, "Gatepass not found", "Invalid Gatepass", []);
        }

        $data = [];
        $data['gatepass_id'] = $gatepass->visitor_gatepass_id;
        $data['visitor_type'] = $gatepass->visitor_type;
        $data['service_vendor_id'] = $gatepass->service_vendor_id ?? 0;
        $data['daily_help_provider_id'] = $gatepass->daily_help_provider_id ?? 0;
        $data['daily_help_service_name'] = optional($gatepass->daily_help_provider)->service_name ?? '';
        if($gatepass->service_vendor_id > 0){
            $data['company_name'] = optional($gatepass->service_vendor)->vendor_company_name ?? '';
            $data['company_icon'] =   isset($gatepass->service_vendor->service_vendor_file) ? url($gatepass->service_vendor->service_vendor_file->file_url) : '';
        }else{
            $data['company_name'] = $gatepass->company_name;
            $data['company_icon'] = '';
        }
        $data['visitor_mobile_no'] = $gatepass->visitor_mobile_no;
        $data['visitor_image'] = isset($gatepass->visitor_image) ? url($gatepass->visitor_image->file_url) : '';
        $data['invitation_message'] = $gatepass->invitation_message;
        $data['total_visitors'] = $gatepass->total_visitors;
        $data['from_date'] = Carbon::parse($gatepass->valid_from_date)->format('Y-m-d');
        $data['to_date'] = Carbon::parse($gatepass->valid_to_date)->format('Y-m-d');
        $data['allowed_days'] = explode(',', $gatepass->allowed_days);
        $data['from_time'] = Carbon::parse($gatepass->valid_from_time)->format('H:i');
        $data['to_time'] = Carbon::parse($gatepass->valid_to_time)->format('H:i');
        $data['block_flat_id'] = $gatepass->block_flat_id;
        $data['society_id'] = $gatepass->society_id;
        $data['added_by_user_id'] = $gatepass->created_by;
        $data['added_by_user_full_name'] = $gatepass->user->full_name;

        return $this->sendResponseWithData($data, "Gatepass details retrieved successfully.");
    }




}
