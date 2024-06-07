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
        ];

        if ($request->input('contact_type') == 2) {
            // Society Emergency Contact
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('emergency_contact', 'mobile_no')->where(function ($query) use ($society_id) {
                    return $query->where('master_id', $society_id)->whereNull('deleted_at');
                })
            ];
        } else {
            // Personal Emergency Contact
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('emergency_contact', 'mobile_no')->where(function ($query) {
                    return $query->where('master_id', Auth::id())->whereNull('deleted_at');
                })
            ];
        }

        if ($request->input('contact_id') != 0) {
            $rules['contact_id'] .= '|exists:emergency_contact,emergency_contact_id,deleted_at,NULL';

            if ($request->input('contact_type') == 2) {
                // Society Emergency Contact
                $rules['mobile_no'] = [
                    'required',
                    'numeric',
                    'digits:10',
                    Rule::unique('emergency_contact', 'mobile_no')
                        ->ignore($request->contact_id, 'emergency_contact_id')
                        ->where(function ($query) use ($society_id) {
                            return $query->where('master_id', $society_id)->whereNull('deleted_at');
                        })
                ];
            } else {
                // Personal Emergency Contact
                $rules['mobile_no'] = [
                    'required',
                    'numeric',
                    'digits:10',
                    Rule::unique('emergency_contact', 'mobile_no')
                        ->ignore($request->contact_id, 'emergency_contact_id')
                        ->where(function ($query) {
                            return $query->where('master_id', Auth::id())->whereNull('deleted_at');
                        })
                ];
            }
        }

        $messages = [
            'mobile_no.unique' => 'The mobile no has already been Added.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Check the maximum limit of personal emergency contacts
        if ($request->input('contact_type') == 3) {
            $personalContactsCount = EmergencyContact::where('master_id', Auth::id())
                ->where('contact_type', 3)
                ->whereNull('deleted_at')
                ->count();

            if ($personalContactsCount >= 3) {
                return $this->sendError(400, 'You can only save a maximum of 3 personal emergency contacts.', "Limit Exceeded", []);
            }
        }

        $designation_id = $this->payload['designation_id'];
        if(getResidentDesignation($designation_id) != "Society Admin" &&  $request->contact_type == 2){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        $emergencyContact = EmergencyContact::find($request->contact_id);
        $action = "updated";
        if (!$emergencyContact) {
            $emergencyContact = new EmergencyContact();
            $action = "saved";
        }else{
            if($emergencyContact->contact_type == 3 && $emergencyContact->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }

            if($emergencyContact->contact_type == 2 && $emergencyContact->master_id != $society_id){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }

            $designation_id = $this->payload['designation_id'];
            if(getResidentDesignation($designation_id) != "Society Admin" &&  $emergencyContact->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
        }
        if($request->contact_type == 2){
            $emergencyContact->master_id = $society_id;
        }else{
            $emergencyContact->master_id = Auth::id();
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
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'contact_type' => 'required|in:1,2,3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $query = EmergencyContact::where('estatus', 1);

        if ($request->contact_type == 1) {
            $query->where('contact_type', $request->contact_type);
        } else if ($request->contact_type == 2) {
            $query->where('contact_type', $request->contact_type)->where('master_id', $society_id);
        } else {
            $query->where('contact_type', $request->contact_type)->where('master_id', $user_id = Auth::id());
        }

        // Order by name in ascending order
        $contacts = $query->orderBy('name', 'ASC')->paginate(10);

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
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society ID not provided.', "Not Found", []);
        }
        $designation_id = $this->payload['designation_id'];
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:emergency_contact,emergency_contact_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $contact = EmergencyContact::find($request->contact_id);
        if($contact->contact_type == 3 && $contact->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        if($contact->contact_type == 2 && $contact->master_id != $society_id){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        if(getResidentDesignation($designation_id) != "Society Admin" &&  $contact->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
        if ($contact) {
            $contact->estatus = 3;
            $contact->save();
            $contact->delete();
        }
        return $this->sendResponseSuccess("contact deleted Successfully.");
    }

}
