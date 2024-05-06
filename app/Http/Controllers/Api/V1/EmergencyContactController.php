<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use App\Models\SocietyMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmergencyContactController extends Controller
{
    public function saveEmergencyContact(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:emergency_contacts,id',
            'contact_type' => 'required|in:2,3',
            'name' => 'required|string|max:100',
            'mobile_no' => 'required|string|max:13',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the user is authorized to save emergency contacts
        $isRequestApproved = EmergencyContact::where('society_id', Auth::user()->society_id)
            ->where('estatus', 1)
            ->exists();

        if (!$isRequestApproved) {
            return response()->json(['message' => 'You are not authorized.'], 401);
        }

        // Save or update the emergency contact
        $emergencyContact = EmergencyContact::find($request->contact_id);
        if (!$emergencyContact) {
            $emergencyContact = new EmergencyContact();
        }

        $emergencyContact->contact_type = $request->contact_type;
        $emergencyContact->name = $request->name;
        $emergencyContact->mobile_no = $request->mobile_no;
        $emergencyContact->save();

        return response()->json(['message' => 'Emergency contact saved successfully.']);
    }

    public function getEmergencyContact(Request $request)
    {
        $contactId = $request->input('contact_id');

        // Fetch the emergency contact
        $emergencyContact = EmergencyContact::find($contactId);

        if (!$emergencyContact) {
            return response()->json(['message' => 'Emergency contact not found.'], 404);
        }

        return response()->json($emergencyContact);
    }

    public function listEmergencyContacts(Request $request)
    {
        $contactType = $request->input('contact_type');

        // Fetch emergency contacts based on the type
        $emergencyContacts = EmergencyContact::where('contact_type', $contactType)->get();

        return response()->json($emergencyContacts);
    }

    public function deleteEmergencyContact(Request $request)
    {
        $contactId = $request->input('contact_id');

        // Fetch the emergency contact
        $emergencyContact = EmergencyContact::find($contactId);

        if (!$emergencyContact) {
            return response()->json(['message' => 'Emergency contact not found.'], 404);
        }

        // Delete the emergency contact
        $emergencyContact->delete();

        return response()->json(['message' => 'Emergency contact deleted successfully.']);
    }
}
