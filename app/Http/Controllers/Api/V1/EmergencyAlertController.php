<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyAlert;
use App\Models\SocietyMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmergencyAlertController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_emergency_alert(Request $request)
    {
        $society_member_id = $this->payload['society_member_id'];
        if (empty($society_member_id)) {
            return $this->sendError(400, 'society member ID not provided.', "Not Found", []);
        }

        $rules = [
            'reason_type' => 'required|in:1,2,3,4',
            'alert_message' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $emergencyContact = new EmergencyAlert();
        $emergencyContact->society_member_id = $society_member_id;
        $emergencyContact->alert_reason_type = $request->reason_type;
        $emergencyContact->alert_message = $request->alert_message;
        $emergencyContact->created_by = Auth::user()->user_id;
        $emergencyContact->created_at = now();
        $emergencyContact->save();

        $data = array();
        $temp['emergency_alert_id'] = $emergencyContact->emergency_alert_id;
        array_push($data, $temp);

        return $this->sendResponseWithData($data, 'emergency alert saved successfully.');
    }

    public function emergency_alert_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'society ID not provided.', "Not Found", []);
        }
        //$contacts = EmergencyAlert::with('society_member.user')->orderBy('created_at', 'desc')->paginate(10);
        $contacts = EmergencyAlert::whereHas('society_member', function ($query) use ($society_id) {
            $query->where('society_id', $society_id);
        })->with('society_member.user')->orderBy('created_at', 'desc')->paginate(10);
        $contact_arr = array();
        foreach ($contacts as $contact) {
            $flat_info = getSocietyBlockAndFlatInfo($contact->society_member['block_flat_id']);
            $temp['alert_id'] = $contact->emergency_alert_id;
            $temp['reason_type'] = getReasonTypeName($contact->alert_reason_type);
            $temp['alert_message'] = $contact->alert_message;
            $temp['full_name'] = $contact->society_member->user->full_name;
            $temp['block_flat_no'] =  $flat_info['block_name'] .'-'. $flat_info['flat_no'];
            $temp['created_time'] = $contact->created_at->format('d-m-Y H:i:s');
            array_push($contact_arr, $temp);
        }

        $data['alert_list'] = $contact_arr;
        $data['total_records'] = $contacts->toArray()['total'];
        return $this->sendResponseWithData($data, "All Alert Retrieved Successfully.");
    }

    public function delete_emergency_alert(Request $request)
    {
        $user_id = Auth::id();
        $validator = Validator::make($request->all(), [
            'alert_id' => 'required|exists:emergency_alert,emergency_alert_id',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $contact = EmergencyAlert::find($request->alert_id);
        if ($contact) {
            $contact->delete();
        }
        return $this->sendResponseSuccess("alert deleted Successfully.");
    }

}
