<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use App\Models\SocietyMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmergencyContactController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }
    public function save_emergency_contact(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society ID not provided.', "Not Found", []);
        }

        $rules = [
            'contact_id' => 'required',
            'contact_type' => 'required|in:2,3',
            'name' => 'required|string|max:100',
            'mobile_no' => 'required|string|max:13',
        ];

        if ($request->input('contact_id') != 0) {
            $rules['contact_id'] .= '|exists:emergency_contact,emergency_contact_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $emergencyContact = EmergencyContact::find($request->contact_id);
        $action = "updated";
        if (!$emergencyContact) {
            $emergencyContact = new EmergencyContact();
            $action = "saved";
        }
        if($request->contact_type == 2){
            $emergencyContact->master_id = $society_id;
        }else{
            $emergencyContact->master_id = $user_id = Auth::id();
        }
        $emergencyContact->contact_type = $request->contact_type;
        $emergencyContact->name = $request->name;
        $emergencyContact->mobile_no = $request->mobile_no;
        $emergencyContact->created_by = Auth::user()->user_id;
        $emergencyContact->updated_by = Auth::user()->user_id;
        $emergencyContact->save();

        $data = array();
        $temp['contact_id'] = $emergencyContact->emergency_contact_id;
        array_push($data, $temp);

        return $this->sendResponseWithData($data, 'Emergency contact '.$action.' successfully.');
    }

    public function emergency_contact_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'contact_type' => 'required|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $query = EmergencyContact::where('estatus', 1);

        if ($request->contact_type == 1) {
            $query->where('contact_type', $request->contact_type);
        }else if($request->contact_type == 2){
            $query->where('contact_type', $request->contact_type)->where('master_id', $society_id);
        }else{
            $query->where('contact_type', $request->contact_type)->where('master_id', $user_id = Auth::id());
        }

        $contacts = $query->paginate(10);
        $contact_arr = array();
        foreach ($contacts as $contact) {
            $temp['contact_id'] = $contact->emergency_contact_id;
            $temp['name'] = $contact->name;
            $temp['mobile_no'] = $contact->mobile_no;
            array_push($contact_arr, $temp);
        }

        $data['contact_list'] = $contact_arr;
        $data['total_records'] = $contacts->toArray()['total'];
        return $this->sendResponseWithData($data, "All Contact Retrieved Successfully.");
    }

    public function delete_emergency_contact(Request $request)
    {
        $user_id = Auth::id();
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:emergency_contact,emergency_contact_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $contact = EmergencyContact::find($request->contact_id);
        if ($contact) {
            $contact->estatus = 3;
            $contact->save();
            $contact->delete();
        }
        return $this->sendResponseSuccess("contact deleted Successfully.");
    }

}