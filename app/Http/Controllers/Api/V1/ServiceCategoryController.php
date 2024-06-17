<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_category(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $category_id = $request->department_id;
        $rules = [
            'category_id' => 'required|numeric',
            'department_id' => 'required|exists:society_department,society_department_id,deleted_at,NULL,society_id,'.$society_id,
            'category_name' => [
                'required',
                'max:50',
                Rule::unique('service_category', 'service_category_name')
                    ->where(function ($query) use ($category_id,$society_id) {
                        return $query->where('society_department_id', $category_id)
                        ->where('society_id', $society_id)
                                     ->whereNull('deleted_at');
                    })
                    ->ignore($request->category_id, 'service_category_id'),
                ],
            'description' => 'nullable|max:400',
        ];
        if ($request->has('category_id') && $request->input('category_id') != 0) {
            $rules['category_id'] .= '|exists:service_category,service_category_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->category_id == 0){
            $category = New ServiceCategory();
            $category->society_id = $society_id;
            $category->created_at = now();
            $category->created_by = Auth::user()->user_id;
            $category->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $category = ServiceCategory::find($request->category_id);
            if($request->calling_by == 1 &&  $category->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
            $category->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $category->society_department_id = $request->department_id;
        $category->service_category_name = $request->category_name;
        $category->category_description = $request->description;
        $category->save();

        $data = array();
        $temp['category_id'] = $category->service_category_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Category ".$action." Successfully");
    }

    public function category_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $rules = [
            'department_id' => 'required|integer',
        ];

        if ($request->has('department_id') && $request->input('department_id') != 0) {
            $rules['department_id'] .= '|exists:society_department,society_department_id,deleted_at,NULL,society_id,' . $society_id;
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Initialize the query
        $query = ServiceCategory::with('department')->where('estatus', 1);

        // Apply department filter if department_id is provided and not zero
        if ($request->department_id != 0) {
            $query->where('society_department_id', $request->department_id);
        }

        // Execute the query and paginate results
        $categories = $query->orderBy('service_category_name', 'ASC')->paginate(10);
        $category_arr = array();
        foreach ($categories as $category) {
            $temp['category_id'] = $category->service_category_id;
            $temp['department_id'] = $category->society_department_id;
            $temp['department_name'] = $category->department->department_name;
            $temp['category_name'] = $category->service_category_name;
            $temp['description'] = $category->category_description;
            array_push($category_arr, $temp);
        }

        $data['category_list'] = $category_arr;
        $data['total_records'] = $categories->toArray()['total'];
        return $this->sendResponseWithData($data, "All category Successfully.");
    }

    public function get_category(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:service_category,service_category_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $category = ServiceCategory::where('service_category_id',$request->category_id)->where('society_id',$society_id)->first();
        if (!$category) {
            return $this->sendError(404, 'category not found.', "Not Found", []);
        }
        $data = array();
        $temp['category_id'] = $category->service_category_id;
        $temp['department_id'] = $category->society_department_id;
        $temp['category_name'] = $category->service_category_name;
        $temp['description'] = $category->category_description;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Category Detail Retrieved Successfully.");
    }

    public function delete_category(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:service_category,service_category_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $category = ServiceCategory::find($request->category_id);
        if($request->calling_by == 1 &&  $category->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        if ($category) {
            $category->estatus = 3;
            $category->save();
            $category->delete();
        }
        return $this->sendResponseSuccess("category deleted Successfully.");
    }
}
