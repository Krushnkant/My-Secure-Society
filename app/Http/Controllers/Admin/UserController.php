<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\User;
use App\Models\UserDesignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $designations = Designation::where('estatus',1)->get();
        return view('admin.users.list',compact('designations'));
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
        $query = User::with('userdesignation')->where('user_id','!=',1)->where('user_type',1);
   
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('full_name', 'like', "%".$search."%");
            $query->orWhere('email', 'like', "%".$search."%");
            $query->orWhere('mobile_no', 'like', "%".$search."%");
        });

        $orderByName = 'full_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'full_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        $formattedData = $data->map(function ($item) {

            return [
                'user_id' => $item->user_id,
                'full_name' => $item->full_name,
                'email' => $item->email,
                'profile_pic_url' => $item->profile_pic_url,
                'mobile_no' => $item->mobile_no,
                'estatus' => $item->estatus,
                'designation' => isset($item->userdesignation->designation)?$item->userdesignation->designation->designation_name:"",
            ];
        });
        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $formattedData], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'profile_pic.image' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'profile_pic.mimes' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'full_name.required' => 'Please provide a FullName',
            'mobile_no.required' => 'Please provide a Mobile No.',
            'email.required' => 'Please provide a Email Address.',
            'password.required' => 'Please provide a Password.',
        ];
        $rules = [
            'profile_pic' => $request->has('profile_pic') ? 'image|mimes:jpeg,png,jpg' : '',
            'full_name' => 'required|max:70',
        ];
        if ($request->has('id') && $request->has('email')) {
            $rules['email'] = [
                'required',
                'email',
                'max:50',
                Rule::unique('user')->ignore($request->id,'user_id')->whereNull('deleted_at'),
            ];
        } elseif ($request->has('email')) {
            $rules['email'] = [
                'required',
                'email',
                'max:50',
                Rule::unique('user')->whereNull('deleted_at'),
            ];
        }
        if ($request->has('id') && $request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->ignore($request->id,'user_id')->whereNull('deleted_at'),
            ];
        } elseif ($request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->whereNull('deleted_at'),
            ];
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $user = new User();
            $user->full_name = $request->full_name;
            $user->user_code = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $user->user_type = 1;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            if ($request->hasFile('profile_pic')) {
                $user->profile_pic_url = $this->uploadProfileImage($request);
            }
            $user->save();

            $this->addUserDesignation($user,$request);
            return response()->json(['status' => '200', 'action' => 'add']);
        }else{
            $user = User::find($request->id);
            if ($user) {
                $old_image = $user->profile_pic_url;
                $user->full_name = $request->full_name;
                $user->email = $request->email;
                $user->mobile_no = $request->mobile_no;
                $user->gender = $request->gender;
                $user->blood_group = $request->blood_group;
                $user->updated_by = Auth::user()->user_id;
                $user->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                if ($request->hasFile('profile_pic')) {
                    $user->profile_pic_url = $this->uploadProfileImage($request,$old_image);
                }
                $user->save();

                $this->addUserDesignation($user,$request);
                return response()->json(['status' => '200', 'action' => 'update']);
            }

            return response()->json(['status' => '400']);
        }

    }

    public function uploadProfileImage($request,$old_image=""){
        $image = $request->file('profile_pic');
        $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('images/profile_pic');
        $image->move($destinationPath, $image_name);
        if(isset($old_image) && $old_image != "") {
            $old_image = public_path($old_image);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        return  'images/profile_pic/'.$image_name;
    }

    public function addUserDesignation($user,$request){
        $user_designation = UserDesignation::where('user_id',$user->user_id)->first();
        if($user_designation){
            $user_designation->company_designation_id = $request->designation;
            $user_designation->updated_by = Auth::user()->user_id;
            $user_designation->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }else{
            $user_designation =  New UserDesignation();
            $user_designation->user_id = $user->user_id;
            $user_designation->company_designation_id = $request->designation;
            $user_designation->updated_by = Auth::user()->user_id;
            $user_designation->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }
        $user_designation->save();
    }


    public function edit($id){
        $user = User::with('userdesignation')->find($id);
        return response()->json($user);
    }

    public function delete($id){
        $user = User::findOrFail($id);
        if ($id == Auth::id()) {
            return response()->json(['status' => '403']);
        }
        if ($user){
            $user->estatus = 3;
            $user->save();
            $user->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $user = User::find($id);
        if ($user->estatus==1){
            $user->estatus = 2;
            $user->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($user->estatus==2){
            $user->estatus = 1;
            $user->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $users = User::whereIn('user_id', $ids)->get();
        foreach ($users as $user) {
            if ($user->user_id != Auth::id()) {
                $user->estatus = 3;
                $user->save();
                $user->delete();
            }
        }
        return response()->json(['status' => '200']);
    }
}
