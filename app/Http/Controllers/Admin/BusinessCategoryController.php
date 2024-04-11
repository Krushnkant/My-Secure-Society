<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BusinessCategoryController extends Controller
{

    public function index() {
        $categories = BusinessCategory::where('estatus',1)->get();
        return view('admin.business_category.list',compact('categories'));
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
        $query = BusinessCategory::with('parent_category')->select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('business_category_name', 'like', "%".$search."%");
        });

        $orderByName = 'business_category_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'business_category_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'business_category_name.required' =>'Please provide a business category name',
            'business_category_name.max' =>'The category name must not exceed :max characters.',
            'business_category_name.unique' =>'The category name has already been taken.',
        ];

        $validator = Validator::make($request->all(), [
            'business_category_name' => [
                'required',
                'max:50',
                Rule::unique('business_category', 'business_category_name')
                    ->ignore($request->id,'business_category_id')
                    ->whereNull('deleted_at'),
            ],
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $category = new BusinessCategory();
            $action = 'add';
        }
        else{
            $category = BusinessCategory::find($request->id);
            if (!$category) {
                return response()->json(['status' => '400']);
            }
            $action = 'update';
        }
        $step_no = 0;
        if(isset($request->parent_business_category_id) && $request->parent_business_category_id != ""){
            $parentcategory = BusinessCategory::find($request->parent_business_category_id);
            $step_no = $parentcategory->step_no + 1;
        }
        $category->business_category_name = $request->business_category_name;
        $category->parent_business_category_id = (isset($request->parent_business_category_id) && $request->parent_business_category_id != "") ? $request->parent_business_category_id : 0 ;
        $category->step_no = $step_no;
        $category->created_by = Auth::user()->user_id;
        $category->updated_by = Auth::user()->user_id;
        $category->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $category->save();


        $newCategoryId = $category->business_category_id;
        $newCategoryName = $category->business_category_name;
        return response()->json(['status' => '200', 'action' =>  $action, 'newCategoryId' => $newCategoryId, 'newCategoryName' => $newCategoryName]);
    }

    public function ajaxlist(Request $request,$id = null){



        $categories = BusinessCategory::where('estatus',1);
        if ($id !== null) {
            $childCategories = $this->getAllChildCategories($id);

            $categories = $categories->where('business_category_id','!=',$id);
            $categories = $categories->whereNotIn('business_category_id',$childCategories);
        }
        $categories = $categories->get();
        return response()->json(['categories' => $categories]);
    }

    private function getAllChildCategories($categoryId)
    {
        $categories = BusinessCategory::where('parent_business_category_id', $categoryId)->get();

        $childCategories = [];
        $childCategories = [$categoryId];
        foreach ($categories as $category) {
            $childCategories[] = $category->business_category_id;
            $childCategories = array_merge($childCategories, $this->getAllChildCategories($category->business_category_id));
        }

        return $childCategories;
    }

    public function edit($id){
        $category = BusinessCategory::find($id);
        return response()->json($category);
    }

    public function delete($id){
        $category = BusinessCategory::find($id);
        if ($category){

            $businessProfiles = \DB::table('business_profile_category')
            ->where('business_category_id', $id)
            ->exists();
            if ($businessProfiles) {
                return response()->json(['status' => '300', 'message' => 'Cannot delete category. It is associated with one or more business profiles.']);
            }
            $category->estatus = 3;
            $category->save();
            $category->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $category = BusinessCategory::find($id);
        if ($category->estatus==1){
            $category->estatus = 2;
            $category->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($category->estatus==2){
            $category->estatus = 1;
            $category->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $categories = BusinessCategory::whereIn('business_category_id', $ids)->pluck('business_category_id');
        $businessProfiles = \DB::table('business_profile_category')
            ->whereIn('business_category_id', $categories)
            ->exists();

        if($businessProfiles) {
            return response()->json(['status' => '300','message'=>"Categories can't be deleted due to some Categories having profile"]);
        }    
        foreach ($categories as $category) {
            $category = BusinessCategory::where('business_category_id',$category)->first();
            $category->estatus = 3;
            $category->save();
            $category->delete();
        }

        return response()->json(['status' => '200']);
    }
}
