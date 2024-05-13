<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodDonate;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BloodDonateController extends BaseController
{

    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function request_blood_donate(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'request_id' => 'required',
            'blood_group' => 'required|string',
            'patient_name' => 'required|string|max:70',
            'relation_with_patient' => 'required|integer|between:1,4',
            'required_blood_bottle_qty' => 'required|integer|between:1,5',
            'expected_time' => 'required|date_format:Y-m-d H:i:s',
            'hospital_name' => 'required|string|max:255',
            'hospital_address' => 'required|string|max:255',
            'landmark' => 'required|string|max:50',
            'pin_code' => 'required|string',
            'city_id' => 'required|integer|exists:city',
            'state_id' => 'required|integer|exists:state',
            'country_id' => 'required|integer|exists:country',
        ];

        if ($request->input('request_id') != 0) {
            $rules['request_id'] .= '|exists:blood_donate_request,blood_donate_request_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->request_id == 0){
            $blood = New BloodDonate();
            $blood->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $blood->created_by = Auth::user()->user_id;
            $blood->updated_by = Auth::user()->user_id;
            $action = "Added";
        }else{
            $blood = BloodDonate::find($request->request_id);
            if (!$blood)
            {
                return $this->sendError(404,'request id Not Exist.', "Not Found Error", []);
            }
            $blood->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $blood->society_id = $society_id;
        $blood->blood_group = $request->blood_group;
        $blood->patient_name = $request->patient_name;
        $blood->relation_with_patient = $request->relation_with_patient;
        $blood->blood_bottle_qty = $request->required_blood_bottle_qty;
        $blood->message = $request->message;
        $blood->expected_time = $request->expected_time;
        $blood->hospital_name = $request->hospital_name;
        $blood->hospital_address = $request->hospital_address;
        $blood->landmark = $request->landmark;
        $blood->pin_code = $request->pin_code;
        $blood->city_id = $request->city_id;
        $blood->state_id = $request->state_id;
        $blood->country_id = $request->country_id;
        $blood->save();

        $data = array();
        $temp['request_id'] = $blood->blood_donate_request_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Request ". $action ." Successfully");
    }


    public function request_blood_donate_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $user_id = Auth::id();

        $block_flat_id = $this->payload['block_flat_id'];
        if (empty($block_flat_id)) {
            return $this->sendError(400, 'Block Flat ID not provided.', "Not Found", []);
        }
        $query = BloodDonate::with('user')->where('society_id', $society_id);

        if (isset($request->blood_group) && $request->blood_group != "") {
            $query->where('blood_group', $request->blood_group);
        }
        $query->orderBy('created_at', 'DESC');
        $perPage = 10;
        $bloods = $query->paginate($perPage);

        $blood_arr = array();
        foreach ($bloods as $blood) {
            $temp['request_id'] = $blood->blood_donate_request_id;
            $temp['request_by_user_id'] = isset($blood->user)?$blood->user->user_id:"";
            $temp['request_by_user_fullname'] = isset($blood->user)?$blood->user->full_name:"";
            $temp['request_by_user_profile_pic'] = isset($blood->user) && $blood->profile_pic_url != ""?url($blood->user->profile_pic_url):"";
            $temp['request_by_user_block_flat_no'] = "";
            $temp['blood_group'] = $blood->blood_group;
            $temp['patient_name'] = $blood->patient_name;
            $temp['relation_with_patient'] = $blood->relation_with_patient;
            $temp['required_blood_bottle_qty'] = $blood->blood_bottle_qty;
            $temp['expected_time'] = $blood->expected_time;
            $temp['hospital_name'] = $blood->hospital_name;
            $temp['hospital_address'] = $blood->hospital_address;
            $temp['landmark'] = $blood->landmark;
            $temp['pin_code'] = $blood->pin_code;
            $temp['city'] = $blood->city;
            $temp['state'] = $blood->state;
            $temp['country'] = $blood->country;
            $temp['request_status'] = $blood->request_status;
            $temp['total_reply'] = $blood->total_reply;
            $temp['confirmed_blood_bottle_qty'] = $blood->confirmed_blood_bottle_qty;
            $temp['request_date'] =  Carbon::parse($blood->created_at)->format('d-m-Y H:i:s');
            $temp['requested_time_str'] = Carbon::parse($blood->created_at)->diffForHumans();
            array_push($blood_arr, $temp);
        }

        $data['request_list'] = $blood_arr;
        $data['total_records'] = $bloods->toArray()['total'];
        return $this->sendResponseWithData($data, "All Request Retrieved Successfully.");
    }



    public function get_request_blood_donate(Request $request)
    {
        $user_id =  Auth::user()->user_id;
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:blood_donate_request,blood_donate_request_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $blood = BloodDonate::with('user')->where('blood_donate_request_id',$request->request_id)->first();
        if (!$blood){
            return $this->sendError(404,"You can not view this folder", "Invalid folder", []);
        }
        $data = array();
        $temp['request_id'] = $blood->blood_donate_request_id;
        $temp['request_by_user_id'] = isset($blood->user)?$blood->user->user_id:"";
        $temp['request_by_user_fullname'] = isset($blood->user)?$blood->user->full_name:"";
        $temp['request_by_user_profile_pic'] = isset($blood->user) && $blood->profile_pic_url != ""?url($blood->user->profile_pic_url):"";
        $temp['request_by_user_block_flat_no'] = "";
        $temp['blood_group'] = $blood->blood_group;
        $temp['patient_name'] = $blood->patient_name;
        $temp['relation_with_patient'] = $blood->relation_with_patient;
        $temp['required_blood_bottle_qty'] = $blood->blood_bottle_qty;
        $temp['expected_time'] = $blood->expected_time;
        $temp['hospital_name'] = $blood->hospital_name;
        $temp['hospital_address'] = $blood->hospital_address;
        $temp['landmark'] = $blood->landmark;
        $temp['pin_code'] = $blood->pin_code;
        $temp['city_id'] = $blood->city_id;
        $temp['state_id'] = $blood->state_id;
        $temp['country_id'] = $blood->country_id;
        // $temp['request_status'] = $blood->request_status;
        // $temp['total_reply'] = $blood->total_reply;
        // $temp['confirmed_blood_bottle_qty'] = $blood->confirmed_blood_bottle_qty;
        // $temp['request_date'] = $blood->request_date;
        // $temp['requested_time_str'] = $blood->requested_time_str;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "All Request Retrieved Successfully.");
    }

    public function change_status_request_blood_donate(Request $request)
    {
        $rules = [
            'request_id' => 'required|exists:blood_donate_request,blood_donate_request_id,deleted_at,NULL',
            'request_status' => 'required|in:2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $blood = BloodDonate::find($request->request_id);
        if ($blood) {
            $blood->request_status = $request->request_status;
            $blood->save();
            if($request->request_status == 3){
                $blood->delete();
            }
        }
        return $this->sendResponseSuccess("request updated Successfully.");
    }

    public function reply_request_blood_donate(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'reply_id' => 'required',
            'request_id' => 'required|exists:blood_donate_request,blood_donate_request_id,deleted_at,NULL',
            'required_blood_bottle_qty' => 'required|integer|min:1',
            'reply_message' => 'required|string|max:255',
        ];

        if ($request->input('reply_id') != 0) {
            $rules['reply_id'] .= '|exists:blood_donate_request_response,blood_donate_request_response_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->reply_id == 0){
            $blood = New BloodDonateRequestResponse();
            $blood->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $blood->created_by = Auth::user()->user_id;
            $blood->updated_by = Auth::user()->user_id;
            $action = "Added";
        }else{
            $blood = BloodDonateRequestResponse::find($request->reply_id);
            if (!$blood)
            {
                return $this->sendError(404,'request id Not Exist.', "Not Found Error", []);
            }
            $blood->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $blood->blood_donate_request_id = $request_id;
        $blood->blood_bottle_qty = $request->required_blood_bottle_qty;
        $blood->message = $request->reply_message;
        $blood->save();

        $data = array();
        $temp['reply_id'] = $blood->blood_donate_request_response_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Reply ". $action ." Successfully");
    }


    public function reply_request_blood_donate_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $user_id = Auth::id();

        $rules = [
            'request_id' => 'required|exists:blood_donate_request,blood_donate_request_id,deleted_at,NULL',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $query = BloodDonateRequestResponse::with('user','request')->where('request_id', $request->request_id);
        $query->orderBy('created_at', 'DESC');
        $perPage = 10;
        $bloods = $query->paginate($perPage);

        $blood_arr = array();
        foreach ($bloods as $blood) {
            $temp['reply_id'] = $blood->blood_donate_request_response_id;
            $temp['request_id'] = $blood->blood_donate_request_id;
            $temp['required_blood_bottle_qty'] = $blood->blood_bottle_qty;
            $temp['reply_message'] = $blood->message;
            $temp['reply_by_user_id'] = isset($blood->user)?$blood->user->user_id:"";
            $temp['reply_by_user_full_name'] = isset($blood->user)?$blood->user->full_name:"";
            $temp['reply_by_user_profile_pic'] = isset($blood->user) && $blood->profile_pic_url != ""?url($blood->user->profile_pic_url):"";
            $temp['reply_by_user_full_name'] = isset($blood->user)?$blood->user->mobile_no:"";
            $temp['request_by_user_block_flat_no'] = "";
            $temp['reply_time'] =  Carbon::parse($blood->created_at)->format('d-m-Y H:i:s');
            $temp['requested_time_str'] = Carbon::parse($blood->request->created_at)->diffForHumans();
            array_push($blood_arr, $temp);
        }

        $data['request_list'] = $blood_arr;
        $data['total_records'] = $bloods->toArray()['total'];
        return $this->sendResponseWithData($data, "All Request Retrieved Successfully.");
    }
}
