<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitingHelpCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VisitingHelpCategoryController extends Controller
{
    public function index() {
        $categories = VisitingHelpCategory::get();
        return view('admin.visiting_help_category.list',compact('categories'));
    }

    public function listdata(Request $request){
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'ASC';

        // get data from products table
        $query = VisitingHelpCategory::select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('visiting_help_category_name', 'like', "%".$search."%");
        });

        $orderByName = 'visiting_help_category_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'visiting_help_category_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'visiting_help_category_name.required' =>'Please provide a business category name',
            'visiting_help_category_name.max' =>'The category name must not exceed :max characters.',
            'visiting_help_category_name.unique' =>'The category name has already been taken.',
        ];

        $validator = Validator::make($request->all(), [
            'visiting_help_category_name' => [
                'required',
                'max:50',
                Rule::unique('visiting_help_category', 'visiting_help_category_name')
                    ->ignore($request->id,'visiting_help_category_id')
                    ->whereNull('deleted_at'),
            ],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $category = new VisitingHelpCategory();
            $action = 'add';
        }
        else{
            $category = VisitingHelpCategory::find($request->id);
            if (!$category) {
                return response()->json(['status' => '400']);
            }
            $action = 'update';
        }

        $category->visiting_help_category_name = $request->visiting_help_category_name;
        $category->created_by = Auth::user()->user_id;
        $category->updated_by = Auth::user()->user_id;
        $category->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $category->save();
        return response()->json(['status' => '200', 'action' =>  $action]);
    }


    public function edit($id){
        $category = VisitingHelpCategory::find($id);
        return response()->json($category);
    }

    public function delete($id){
        $category = VisitingHelpCategory::find($id);
        if ($category){
            $category->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }


    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $categories = VisitingHelpCategory::whereIn('visiting_help_category_id', $ids)->pluck('visiting_help_category_id');

        foreach ($categories as $category) {
            $category = VisitingHelpCategory::where('visiting_help_category_id',$category)->first();
            $category->delete();
        }

        return response()->json(['status' => '200']);
    }
}
