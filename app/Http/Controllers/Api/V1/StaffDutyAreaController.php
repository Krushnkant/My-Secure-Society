<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StaffDutyArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StaffDutyAreaController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_duty_area(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'staff_duty_area_id' => 'required|numeric',
            'area_name' => [
                'required',
                'max:50',
                Rule::unique('staff_duty_area', 'area_name')
                    ->where(function ($query) use ($society_id) {
                        return $query->where('society_id', $society_id)
                                     ->whereNull('deleted_at');
                    })
                    ->ignore($request->staff_duty_area_id, 'staff_duty_area_id'),
            ],
            'society_id' => 'required|exists:society',
        ];
        if ($request->has('staff_duty_area_id') && $request->input('staff_duty_area_id') != 0) {
            $rules['staff_duty_area_id'] .= '|exists:staff_duty_area,staff_duty_area_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->staff_duty_area_id == 0){
            $area = New StaffDutyArea();
            $area->society_id = $society_id;
            $area->created_at = now();
            $area->created_by = Auth::user()->user_id;
            $area->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $area = StaffDutyArea::find($request->staff_duty_area_id);
            if($request->calling_by == 1 &&  $area->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
            $area->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $area->area_name = $request->area_name;
        $area->save();

        $data = array();
        $temp['staff_duty_area_id'] = $area->staff_duty_area_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Duty Area ".$action." Successfully");
    }

    public function duty_area_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $areas = StaffDutyArea::where('society_id', $society_id)->where('estatus', 1)->orderBy('area_name', 'ASC')->paginate(10);
        $area_arr = array();
        foreach ($areas as $area) {
            $temp['staff_duty_area_id'] = $area['staff_duty_area_id'];
            $temp['area_name'] = $area->area_name;
            array_push($area_arr, $temp);
        }

        $data['visit_area_list'] = $area_arr;
        $data['total_records'] = $areas->toArray()['total'];
        return $this->sendResponseWithData($data, "All Duty Area Successfully.");
    }

    public function get_duty_area(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'staff_duty_area_id' => 'required|exists:staff_duty_area,staff_duty_area_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area = StaffDutyArea::find($request->staff_duty_area_id);

        if (!$area) {
            return response()->json(['error' => 'area not found'], 404);
        }

        $data = array();
        $temp['staff_duty_area_id'] = $area['staff_duty_area_id'];
        $temp['area_name'] = $area->area_name;
        array_push($data, $temp);

        return $this->sendResponseWithData($data, "All Duty Area Retrieved Successfully.");
    }

    public function delete_duty_area(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'staff_duty_area_id' => 'required|exists:staff_duty_area,staff_duty_area_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $area = StaffDutyArea::find($request->staff_duty_area_id);
        if($request->calling_by == 1 &&  $area->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
        if ($area) {
            $area->estatus = 3;
            $area->save();
            $area->delete();
        }
        return $this->sendResponseSuccess("duty area deleted Successfully.");
    }
}
