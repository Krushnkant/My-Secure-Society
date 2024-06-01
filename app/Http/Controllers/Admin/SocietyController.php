<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Country;
use App\Models\ResidentDesignation;
use App\Models\ResidentDesignationAuth;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SocietyController extends Controller
{
    public function index()
    {
        $countries = Country::get();
        return view('admin.society.list', compact('countries'));
    }

    public function listdata(Request $request)
    {

        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'ASC';

        // get data from products table
        $query = Society::select('*')->with('city','state','country');
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('society_name', 'like', "%" . $search . "%");
        });

        $orderByName = 'society_name';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'society_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        return response()->json(["draw" => $request->draw, "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request)
    {
        $messages = [
            'society_name.required' => 'Please provide a society name',
            'street_address1.required' => 'Please provide a street address',
            'landmark.required' => 'Please provide a landmark',
            'pin_code.required' => 'Please provide a pin code',
            'city_id.required' => 'Please provide a city',
            'latitude.required' => 'Please provide a latitude',
            'longitude.required' => 'Please provide a longitude',
            'state_id.required' => 'Please provide a state',
            'country_id.required' => 'Please provide a country',
        ];
        $validator = Validator::make($request->all(), [
            'society_name' => 'required|max:100',
            'street_address1' => 'required|max:255',
            'street_address2' => 'max:255',
            'landmark' => 'required|max:50',
            'pin_code' => 'required|numeric',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }
        if (!isset($request->id)) {
            $society = new Society();
            $society->created_by = Auth::user()->user_id;
            $action = "add";
        } else {
            $society = Society::find($request->id);
            if (!$society) {
                return response()->json(['status' => '400']);
            }
            $action = "update";
        }
        $society->society_name = $request->society_name;
        $society->street_address1 = $request->street_address1;
        $society->street_address2 = $request->street_address2;
        $society->landmark = $request->landmark;
        $society->pin_code = $request->pin_code;
        $society->latitude = $request->latitude;
        $society->longitude = $request->longitude;
        $society->city_id = $request->city_id;
        $society->state_id = $request->state_id;
        $society->country_id = $request->country_id;
        $society->updated_by = Auth::user()->user_id;
        $society->save();

        if($action == "add"){
             $designation_array = ['Society Admin','Committee Member','Society Member'];
             foreach($designation_array as $designation){

                $resident_designation = new ResidentDesignation();
                $resident_designation->society_id = $society->society_id;
                $resident_designation->designation_name = $designation;
                $resident_designation->can_update_authority_claims = 1;
                $resident_designation->created_by = Auth::user()->user_id;
                $resident_designation->updated_by = Auth::user()->user_id;
                $resident_designation->save();

                if($resident_designation){
                    if($designation == 'Society Admin'){
                        $DesignationAuthority = array(
                            // Resident's Authority
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 1, // Own Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 2, // Own Family Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 3, // Own Festival Banner
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 4, // Own Festival Banner Configuration
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 5, // Own Folder
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 6, // Own Documents
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 7, // Society Member List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 8, // Announcement
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 9, // Resident's Daily Post
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 10, // Own Daily Post
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 11, // Amenity
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 12, // Amenity Booking
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 13, // Emergency Alert
                                "can_view" => 0,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 0,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 14, // My Emergency No
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 15, // Soc Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 16, // Government Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 17, // Resident's Business Profile
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 18, // Own Business Profile
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 19, // Resident's Society Payment
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 20, // Invoice
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 21, // Own Loan Request
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 22, // Own Complaint
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 23, // Staff Member
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 24, // Staff Member Duty Area
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 25, // Staff Member Attendance
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 26, // Maintanance Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 27, // Loan Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 28, // Pre Approved List
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 29, // Own Visitor List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 30, // Delivered At Gate
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 31, // Daily Help Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 0,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 32, // Daily Help Member for My Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 51, // Society Department
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 52, // Category for Society
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 53, // Society Member Designation
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 54, // Society Member Designation Authority
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 55, // Society Member List
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 56, // Society Member Request
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 57, // Announcement
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 58, // Amenity
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 59, // Amenity Booking
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 60, // Emergency Alert History
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 61, // Society Emergency No
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 62, // Resident's Society Payment
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 63, // Invoice
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 64, // Resident's Loan Request
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 65, // Resident's Complaint
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 66, // Duty Area
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 67, // Staff Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 68, // Staff Member Duty Area
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 69, // Staff Member Attendance
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 70, // Maintanance Terms
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 71, // Loan Terms
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 72, // Pre Approved List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 73, // Visitor List
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 74, // Delivered At Gate
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 75, // Daily Help Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),

                        );
                    }else if($designation == 'Committee Member'){
                        $DesignationAuthority = array(
                            // Resident's Authority
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 1, // Own Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 2, // Own Family Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 3, // Own Festival Banner
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,
                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 4, // Own Festival Banner Configuration
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 5, // Own Folder
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 6, // Own Documents
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 7, // Society Member List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 8, // Announcement
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 9, // Resident's Daily Post
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 10, // Own Daily Post
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 11, // Amenity
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 12, // Amenity Booking
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 13, // Emergency Alert
                                "can_view" => 0,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 0,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 14, // My Emergency No
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 15, // Soc Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 16, // Government Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 17, // Resident's Business Profile
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 18, // Own Business Profile
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 19, // Resident's Society Payment
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 20, // Invoice
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 21, // Own Loan Request
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 22, // Own Complaint
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 23, // Staff Member
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 24, // Staff Member Duty Area
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 25, // Staff Member Attendance
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 26, // Maintanance Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 27, // Loan Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 28, // Pre Approved List
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 29, // Own Visitor List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 30, // Delivered At Gate
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 31, // Daily Help Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 32, // Daily Help Member for My Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),

                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 51, // Society Department
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 52, // Category for Society
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 53, // Society Member Designation
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 54, // Society Member Designation Authority
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 2,
                                "can_delete" => 0,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 55, // Society Member List
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 56, // Society Member Request
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 57, // Announcement
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 58, // Amenity
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 59, // Amenity Booking
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 2,
                                "can_delete" => 0,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 60, // Emergency Alert History
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 61, // Society Emergency No
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 62, // Resident's Society Payment
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 63, // Invoice
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 64, // Resident's Loan Request
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 65, // Resident's Complaint
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 66, // Duty Area
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 67, // Staff Member
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 68, // Staff Member Duty Area
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 69, // Staff Member Attendance
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 70, // Maintanance Terms
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 71, // Loan Terms
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 72, // Pre Approved List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 73, // Visitor List
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 74, // Delivered At Gate
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 75, // Daily Help Member
                                "can_view" => 1,
                                "can_add" => 2,
                                "can_edit" => 2,
                                "can_delete" => 2,
                                "can_print" => 2,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),

                        );
                    }else {
                        $DesignationAuthority = array(
                            // Resident's Authority
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 1, // Own Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 2, // Own Family Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 3, // Own Festival Banner
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 4, // Own Festival Banner Configuration
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 5, // Own Folder
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 6, // Own Documents
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 7, // Society Member List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 8, // Announcement
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 9, // Resident's Daily Post
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 10, // Own Daily Post
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 11, // Amenity
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 12, // Amenity Booking
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 13, // Emergency Alert
                                "can_view" => 0,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 0,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 14, // My Emergency No
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 15, // Soc Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 16, // Government Emergency No
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 17, // Resident's Business Profile
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 18, // Own Business Profile
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 19, // Resident's Society Payment
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 20, // Invoice
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 21, // Own Loan Request
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 22, // Own Complaint
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 23, // Staff Member
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 24, // Staff Member Duty Area
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 25, // Staff Member Attendance
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 26, // Maintanance Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 27, // Loan Terms
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 28, // Pre Approved List
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 29, // Own Visitor List
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 30, // Delivered At Gate
                                "can_view" => 1,
                                "can_add" => 0,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 31, // Daily Help Member
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 0,
                                "can_delete" => 0,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),
                            array(
                                'resident_designation_id' => $resident_designation->resident_designation_id,
                                "eAuthority" => 32, // Daily Help Member for My Flat
                                "can_view" => 1,
                                "can_add" => 1,
                                "can_edit" => 1,
                                "can_delete" => 1,
                                "can_print" => 1,

                                "updated_at" => now(),
                                "updated_by" => 1
                            ),

                        );
                    }
                    DB::table('resident_designate_auth')->insert($DesignationAuthority);
                }
            }
        }
        // else{
        //     $designation_array = ResidentDesignation::where('society_id',$society->society_id)->get();
        //     foreach($designation_array as $resident_designation){
        //         $designation  = $resident_designation->designation_name;
        //         if($resident_designation){
        //             if($designation == 'Society Admin'){
        //                 $DesignationAuthority = array(
        //                     // Resident's Authority
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 1, // Own Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 2, // Own Family Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 3, // Own Festival Banner
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 4, // Own Festival Banner Configuration
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 5, // Own Folder
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 6, // Own Documents
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 7, // Society Member List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 8, // Announcement
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 9, // Resident's Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 10, // Own Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 11, // Amenity
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 12, // Amenity Booking
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 13, // Emergency Alert
        //                         "can_view" => 0,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 0,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 14, // My Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 15, // Soc Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 16, // Government Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 17, // Resident's Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 18, // Own Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 19, // Resident's Society Payment
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 20, // Invoice
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 21, // Own Loan Request
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 22, // Own Complaint
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 23, // Staff Member
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 24, // Staff Member Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 25, // Staff Member Attendance
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 26, // Maintanance Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 27, // Loan Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 28, // Pre Approved List
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 29, // Own Visitor List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 30, // Delivered At Gate
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 31, // Daily Help Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 0,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 32, // Daily Help Member for My Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 51, // Society Department
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 52, // Category for Society
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 53, // Society Member Designation
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 54, // Society Member Designation Authority
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 55, // Society Member List
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 56, // Society Member Request
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 57, // Announcement
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 58, // Amenity
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 59, // Amenity Booking
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 60, // Emergency Alert History
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 61, // Society Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 62, // Resident's Society Payment
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 63, // Invoice
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 64, // Resident's Loan Request
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 65, // Resident's Complaint
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 66, // Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 67, // Staff Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 68, // Staff Member Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 69, // Staff Member Attendance
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 70, // Maintanance Terms
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 71, // Loan Terms
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 72, // Pre Approved List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 73, // Visitor List
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 74, // Delivered At Gate
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 75, // Daily Help Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),

        //                 );
        //             }else if($designation == 'Committee Member'){
        //                 $DesignationAuthority = array(
        //                     // Resident's Authority
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 1, // Own Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 2, // Own Family Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 3, // Own Festival Banner
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,
        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 4, // Own Festival Banner Configuration
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 5, // Own Folder
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 6, // Own Documents
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 7, // Society Member List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 8, // Announcement
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 9, // Resident's Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 10, // Own Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 11, // Amenity
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 12, // Amenity Booking
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 13, // Emergency Alert
        //                         "can_view" => 0,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 0,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 14, // My Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 15, // Soc Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 16, // Government Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 17, // Resident's Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 18, // Own Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 19, // Resident's Society Payment
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 20, // Invoice
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 21, // Own Loan Request
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 22, // Own Complaint
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 23, // Staff Member
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 24, // Staff Member Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 25, // Staff Member Attendance
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 26, // Maintanance Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 27, // Loan Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 28, // Pre Approved List
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 29, // Own Visitor List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 30, // Delivered At Gate
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 31, // Daily Help Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 32, // Daily Help Member for My Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),

        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 51, // Society Department
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 52, // Category for Society
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 53, // Society Member Designation
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 54, // Society Member Designation Authority
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 2,
        //                         "can_delete" => 0,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 55, // Society Member List
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 56, // Society Member Request
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 57, // Announcement
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 58, // Amenity
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 59, // Amenity Booking
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 2,
        //                         "can_delete" => 0,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 60, // Emergency Alert History
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 61, // Society Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 62, // Resident's Society Payment
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 63, // Invoice
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 64, // Resident's Loan Request
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 65, // Resident's Complaint
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 66, // Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 67, // Staff Member
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 68, // Staff Member Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 69, // Staff Member Attendance
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 70, // Maintanance Terms
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 71, // Loan Terms
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 72, // Pre Approved List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 73, // Visitor List
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 74, // Delivered At Gate
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 75, // Daily Help Member
        //                         "can_view" => 1,
        //                         "can_add" => 2,
        //                         "can_edit" => 2,
        //                         "can_delete" => 2,
        //                         "can_print" => 2,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),

        //                 );
        //             }else {
        //                 $DesignationAuthority = array(
        //                     // Resident's Authority
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 1, // Own Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 2, // Own Family Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 3, // Own Festival Banner
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 4, // Own Festival Banner Configuration
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 5, // Own Folder
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 6, // Own Documents
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 7, // Society Member List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 8, // Announcement
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 9, // Resident's Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 10, // Own Daily Post
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 11, // Amenity
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 12, // Amenity Booking
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 13, // Emergency Alert
        //                         "can_view" => 0,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 0,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 14, // My Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 15, // Soc Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 16, // Government Emergency No
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 17, // Resident's Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 18, // Own Business Profile
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 19, // Resident's Society Payment
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 20, // Invoice
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 21, // Own Loan Request
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 22, // Own Complaint
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 23, // Staff Member
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 24, // Staff Member Duty Area
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 25, // Staff Member Attendance
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 26, // Maintanance Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 27, // Loan Terms
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 28, // Pre Approved List
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 29, // Own Visitor List
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 30, // Delivered At Gate
        //                         "can_view" => 1,
        //                         "can_add" => 0,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 31, // Daily Help Member
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 0,
        //                         "can_delete" => 0,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),
        //                     array(
        //                         'resident_designation_id' => $resident_designation->resident_designation_id,
        //                         "eAuthority" => 32, // Daily Help Member for My Flat
        //                         "can_view" => 1,
        //                         "can_add" => 1,
        //                         "can_edit" => 1,
        //                         "can_delete" => 1,
        //                         "can_print" => 1,

        //                         "updated_at" => now(),
        //                         "updated_by" => 1
        //                     ),

        //                 );
        //             }
        //             DB::table('resident_designate_auth')->insert($DesignationAuthority);
        //         }
        //     }
        // }
        return response()->json(['status' => '200', 'action' => $action]);
    }
    public function edit($id)
    {
        $society = Society::find($id);
        return response()->json($society);
    }
    public function delete($id)
    {
        $society = Society::find($id);
        if ($society) {
            $block = Block::where('society_id', $id)->exists();
            if ($block) {
                return response()->json(['status' => '300', 'message' => 'Cannot delete society. It is associated with one or more society block.']);
            }
            $society->estatus = 3;
            $society->save();
            $society->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id)
    {
        $society = Society::find($id);
        if ($society->estatus == 1) {
            $society->estatus = 2;
            $society->save();
            return response()->json(['status' => '200', 'action' => 'deactive']);
        }
        if ($society->estatus == 2) {
            $society->estatus = 1;
            $society->save();
            return response()->json(['status' => '200', 'action' => 'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $societies = Society::whereIn('society_id', $ids)->pluck('society_id');
        $block = Block::whereIn('society_id', $societies)->exists();
        if ($block) {
            return response()->json(['status' => '300','message'=>"Societies can't be deleted due to some Societies having blocks."]);
        }
        foreach ($societies as $societie) {
            $societie = Society::where('society_id', $societie)->first();
                $societie->estatus = 3;
                $societie->save();
                $societie->delete();
            }

        return response()->json(['status' => '200']);
    }
}
