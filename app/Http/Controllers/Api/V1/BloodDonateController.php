<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BloodDonate;
use JWTAuth;

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
            'city_id' => 'required|integer',
            'state_id' => 'required|integer',
            'country_id' => 'required|integer',
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


    public function blood_donate_request_list(Request $request)
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
        $query = BloodDonate::where('society_id', $society_id);

        if (isset($request->blood_group) && $request->blood_group != "") {
            $query->where('blood_group', $request->blood_group);
        }
        $query->orderBy('created_at', 'DESC');
        $perPage = 10;
        $bloods = $query->paginate($perPage);

        $blood_arr = array();
        foreach ($bloods as $blood) {
            $temp['request_id'] = $blood->request_id;
            $temp['request_by_user_id'] = $blood->request_by_user_id;
            $temp['request_by_user_fullname'] = $blood->request_by_user_fullname;
            $temp['request_by_user_profile_pic'] = $blood->request_by_user_profile_pic;
            $temp['request_by_user_block_flat_no'] = $blood->request_by_user_block_flat_no;
            $temp['blood_group'] = $blood->blood_group;
            $temp['patient_name'] = $blood->patient_name;
            $temp['relation_with_patient'] = $blood->relation_with_patient;
            $temp['required_blood_bottle_qty'] = $blood->required_blood_bottle_qty;
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
            $temp['request_date'] = $blood->request_date;
            $temp['requested_time_str'] = $blood->requested_time_str;
            array_push($blood_arr, $temp);
        }

        $data['request_list'] = $blood_arr;
        $data['total_records'] = $documents->toArray()['total'];
        return $this->sendResponseWithData($data, "All Request Retrieved Successfully.");
    }
}
