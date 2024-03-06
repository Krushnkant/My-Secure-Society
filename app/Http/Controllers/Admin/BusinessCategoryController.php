<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BusinessCategoryController extends Controller
{
    
    public function index() {
        return view('admin.business_category.list');
    }

    public function alldesignationlist(Request $request){
      
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = Designation::select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('designation_name', 'like', "%".$search."%");
        });

        $orderByName = 'designation_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'designation_name';
                break;  
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdatedesignation(Request $request){
        $messages = [
            'designation_name.required' =>'Please provide a designation name',
        ];

        $validator = Validator::make($request->all(), [
            'designation_name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $designation = new Designation();
            $designation->designation_name = $request->designation_name;
            $designation->created_by = Auth::user()->user_id;
            $designation->updated_by = Auth::user()->user_id;
            $designation->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $designation->save();
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $designation = Designation::find($request->id);
            if ($designation) {
                $designation->designation_name = $request->designation_name;
                $designation->updated_by = Auth::user()->user_id;
                $designation->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    public function edit($id){
        $designation = Designation::find($id);
        return response()->json($designation);
    }

    public function delete($id){
        $designation = Designation::find($id);
        if ($designation){
            $designation->estatus = 3;
            $designation->save();
            $designation->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $designation = Designation::find($id);
        if ($designation->estatus==1){
            $designation->estatus = 2;
            $designation->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($designation->estatus==2){
            $designation->estatus = 1;
            $designation->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        Designation::whereIn('company_designation_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }
}
