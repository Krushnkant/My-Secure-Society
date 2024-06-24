<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ResidentDesignation;
use App\Models\ResidentDesignationAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DesignationController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_designation(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'designation_id' => 'required|numeric',
            'designation_name' => [
                'required',
                'max:60',
                Rule::unique('resident_designation', 'designation_name')
                ->ignore($request->designation_id, 'resident_designation_id')
                ->where('society_id',$society_id)
                    ->whereNull('deleted_at'),
            ]
        ];
        if ($request->has('designation_id') && $request->input('designation_id') != 0) {
            $rules['designation_id'] .= '|exists:resident_designation,resident_designation_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->designation_id == 0){
            $designation = New ResidentDesignation();
            $designation->society_id = $society_id;
            $designation->use_for = $request->use_for;
            $designation->created_at = now();
            $designation->created_by = Auth::user()->user_id;
            $designation->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $designation = ResidentDesignation::find($request->designation_id);
            if($request->calling_by == 1 &&  $designation->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
            $designation->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $designation->designation_name = $request->designation_name;
        $designation->save();
        if($action == "Added"){
           if($request->use_for == 1){
              $this->defalt_resident_permission($designation->resident_designation_id);
           }
           $this->defalt_admin_permission($designation->resident_designation_id);
        }

        $data = array();
        $temp['designation_id'] = $designation->resident_designation_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Designation ".$action." Successfully");
    }


    protected function defalt_resident_permission($id){
        $DesignationAuthority = array(
            // Resident's Authority
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 19, // Resident's Society Payment
                "can_view" => 1,
                "can_add" => 1,
                "can_edit" => 0,
                "can_delete" => 0,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 31, // Daily Help Member
                "can_view" => 1,
                "can_add" => 0,
                "can_edit" => 0,
                "can_delete" => 0,
                "can_print" => 0,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 33, // Society Department
                "can_view" => 1,
                "can_add" => 2,
                "can_edit" => 2,
                "can_delete" => 2,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
                "eAuthority" => 34, // Service Category
                "can_view" => 1,
                "can_add" => 2,
                "can_edit" => 2,
                "can_delete" => 2,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),

        );
        DB::table('resident_designate_auth')->insert($DesignationAuthority);
    }

    protected function defalt_admin_permission($id){
        $DesignationAuthority = array(
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 60, // Emergency Alert History
                "can_view" => 1,
                "can_add" => 0,
                "can_edit" => 0,
                "can_delete" => 1,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 62, // Resident's Society Payment
                "can_view" => 1,
                "can_add" => 1,
                "can_edit" => 1,
                "can_delete" => 0,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
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
                'resident_designation_id' => $id,
                "eAuthority" => 75, // Daily Help Member
                "can_view" => 1,
                "can_add" => 1,
                "can_edit" => 1,
                "can_delete" => 1,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),
            array(
                'resident_designation_id' => $id,
                "eAuthority" => 76, // Service Category
                "can_view" => 1,
                "can_add" => 1,
                "can_edit" => 1,
                "can_delete" => 1,
                "can_print" => 1,
                "updated_at" => now(),
                "updated_by" => 1
            ),

        );
        DB::table('resident_designate_auth')->insert($DesignationAuthority);
    }

    public function designation_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $rules = [
            'use_for' => 'required|in:0,1,2',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }


        // Get request parameters
        $search_text = $request->input('search_text', '');
        $use_for = $request->input('use_for', 0);

        // Initialize the query
        $query = ResidentDesignation::where('society_id', $society_id);

        // Apply search filter if search_text is provided
        if (!empty($search_text)) {
            $query->where('designation_name', 'LIKE', '%' . $search_text . '%');
        }

        if ($use_for != 0) {
            $query->where('use_for', $use_for);
        }

        $designation_id = $this->payload['designation_id'];
        if(getResidentDesignation($designation_id) == "Society Member"){
            $query->where('estatus',1);
        }
        // Order by designation name and paginate
        $designations = $query->orderBy('designation_name', 'ASC')->paginate(10);
        $designation_arr = array();
        foreach ($designations as $designation) {
            $temp['designation_id'] = $designation->resident_designation_id;
            $temp['designation_name'] = $designation->designation_name;
            $temp['can_update_permission'] = $designation->can_update_authority_claims ? True : False;
            $temp['use_for'] = (int) $designation->use_for;
            array_push($designation_arr, $temp);
        }

        $data['designation_list'] = $designation_arr;
        $data['total_records'] = $designations->total();
        return $this->sendResponseWithData($data, "All Designation Successfully.");
    }

    public function get_designation(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'designation_id' => 'required|exists:resident_designation,resident_designation_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $designation = ResidentDesignation::find($request->designation_id);

        if (!$designation) {
            return response()->json(['error' => 'designation not found'], 404);
        }

        $data = array();
        $temp['designation_id'] = $designation->resident_designation_id;
        $temp['designation_name'] = $designation->designation_name;
        $temp['use_for'] = (int) $designation->use_for;
        array_push($data, $temp);

        return $this->sendResponseWithData($data, "Get Designation Successfully.");
    }

    public function change_status(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        // Validation rules
        $validator = Validator::make($request->all(), [
            'designation_id' => 'required|exists:resident_designation,resident_designation_id,deleted_at,NULL,society_id,'.$society_id,
            'status' => 'required|in:1,2,3',
        ]);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        // Update the status
        $designation = ResidentDesignation::where('resident_designation_id', $request->designation_id)->firstOrFail();
        if ($designation->estatus == $request->status) {
            return $this->sendError(400, "You can't Update the Status, The designation is already in the requested status.", "Bad Request", []);
        }
        $designation->estatus = $request->status;
        $designation->save();
        if($request->status == 3){
            $designation->delete();
        }

        return $this->sendResponseSuccess("Status Updated Successfully.");
    }

    public function get_designation_authority(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'designation_id' => 'required|exists:resident_designation,resident_designation_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $authorities = ResidentDesignationAuthority::where('resident_designation_id',$request->designation_id)->get();
        $authority_arr = array();
        foreach ($authorities as $authority) {
            $temp['authority_id'] = $authority->resident_designate_auth_id;
            $temp['eauthority'] = $authority->eauthority;
            $temp['authority_name'] = getAuthName($authority->eauthority);
            $temp['can_view'] = $authority->can_view;
            $temp['can_add'] = $authority->can_add;
            $temp['can_edit'] = $authority->can_edit;
            $temp['can_delete'] = $authority->can_delete;
            $temp['can_print'] = $authority->can_print;
            array_push($authority_arr, $temp);
        }
        $data['authority_list'] = $authority_arr;
        return $this->sendResponseWithData($data, "All Designation Authority Successfully.");
    }

    // public function set_designation_authority(Request $request)
    // {
    //     $society_id = $this->payload['society_id'];
    //     if($society_id == ""){
    //         return $this->sendError(400,'Society Not Found.', "Not Found", []);
    //     }

    //     $rules = [
    //         'authority_id' => 'required|numeric|exists:resident_designate_auth,resident_designate_auth_id',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return $this->sendError(422,$validator->errors(), "Validation Errors", []);
    //     }

    //     $authority = ResidentDesignationAuthority::find($request->authority_id);
    //     $authority->updated_by = Auth::user()->user_id;
    //     $authority->can_view = $request->can_view;
    //     $authority->can_add = $request->can_add;
    //     $authority->can_edit = $request->can_edit;
    //     $authority->can_delete = $request->can_delete;
    //     $authority->can_print = $request->can_print;
    //     $authority->save();

    //     $data = array();
    //     $temp['authority_id'] = $authority->resident_designate_auth_id;
    //     array_push($data, $temp);
    //     return $this->sendResponseWithData($data, "authority set Successfully");
    // }

    public function set_designation_authority(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'authorities' => 'required|array|min:1',
            'authorities.*.authority_id' => 'required|numeric|exists:resident_designate_auth,resident_designate_auth_id',
            'authorities.*.can_view' => 'required|in:0,1,2',
            'authorities.*.can_add' => 'required|in:0,1,2',
            'authorities.*.can_edit' => 'required|in:0,1,2',
            'authorities.*.can_delete' => 'required|in:0,1,2',
            'authorities.*.can_print' => 'required|in:0,1,2',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $data = array();

        foreach ($request->authorities as $authorityData) {
            $authority = ResidentDesignationAuthority::find($authorityData['authority_id']);
            $designation = ResidentDesignation::find($authority->resident_designation_id);

            // Check if the designation belongs to the same society
            if ($designation->society_id != $society_id) {
                return $this->sendError(403, 'You do not have permission to update this authority.', "Forbidden", []);
            }

            $authority->updated_by = Auth::user()->user_id;

            if ($authority->can_view != 0 && $authorityData['can_view'] != 0) {
                $authority->can_view = $authorityData['can_view'];
            }
            if ($authority->can_add != 0 && $authorityData['can_add'] != 0) {
                $authority->can_add = $authorityData['can_add'];
            }
            if ($authority->can_edit != 0 && $authorityData['can_edit'] != 0) {
                $authority->can_edit = $authorityData['can_edit'];
            }
            if ($authority->can_delete != 0 && $authorityData['can_delete'] != 0) {
                $authority->can_delete = $authorityData['can_delete'];
            }
            if ($authority->can_print != 0 && $authorityData['can_print'] != 0) {
                $authority->can_print = $authorityData['can_print'];
            }

            $authority->save();

            $temp['authority_id'] = $authority->resident_designate_auth_id;
            array_push($data, $temp);
        }

        return $this->sendResponseWithData($data, "Authorities set successfully");
    }



}
