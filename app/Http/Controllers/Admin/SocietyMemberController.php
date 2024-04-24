<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\Flat;
use App\Models\ResidentDesignation;
use App\Models\Society;
use App\Models\SocietyMember;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SocietyMemberController extends Controller
{
    public function index($id) {
        $society = Society::find($id);
        if($society == null){
            return view('admin.404');
        }
        $resident_designations = ResidentDesignation::where('estatus', 1)
        ->where(function ($query) use ($id) {
            $query->where('society_id', $id)
                ->orWhere('society_id', 0);
        })->get();

        $blocks = Block::where('estatus', 1)->where('society_id', $id)->get();
        return view('admin.society_member.list',compact('society','id','resident_designations','blocks'));
    }

    public function listdata(Request $request){
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'ASC';

      
        $query = SocietyMember::select('user.*','society_member.society_member_id','society_member.resident_designation_id','resident_designation.designation_name','block_flat.flat_no','society_block.block_name')
        ->leftJoin('user', 'society_member.user_id', '=', 'user.user_id')
        ->leftJoin('resident_designation', 'society_member.resident_designation_id', '=', 'resident_designation.resident_designation_id')
        ->leftJoin('block_flat', 'society_member.block_flat_id', '=', 'block_flat.block_flat_id')
        ->leftJoin('society_block', 'society_block.society_block_id', '=', 'block_flat.society_block_id')
        ->where('society_member.society_id', $request->society_id);

        $search = $request->search;
        $query = $query->where(function($query) use ($search) {
            $query->orWhere('user.full_name', 'like', "%".$search."%");
        });
        $orderByName = 'user.full_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'user.full_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'full_name.required' => 'Please provide a FullName',
            'mobile_no.required' => 'Please provide a Mobile No.',
            'email.required' => 'Please provide a Email Address.',
            'password.required' => 'Please provide a Password.',
        ];
        $rules = [
            'full_name' => 'required|max:70',
            'block_id' => 'required',
            'flat_id' => 'required',
        ];
        if ($request->has('id') && $request->has('email')) {
            $rules['email'] = [
                'required',
                'email',
                'max:50',
                Rule::unique('user')->ignore($request->id,'user_id')->where('user_type',2)->whereNull('deleted_at'),
            ];
        } elseif ($request->has('email')) {
            $rules['email'] = [
                'required',
                'email',
                'max:50',
                Rule::unique('user')->where('user_type',2)->whereNull('deleted_at'),
            ];
        }
        if ($request->has('id') && $request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->ignore($request->id,'user_id')->where('user_type',2)->whereNull('deleted_at'),
            ];
        } elseif ($request->has('mobile_no')) {
            $rules['mobile_no'] = [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('user')->where('user_type',2)->whereNull('deleted_at'),
            ];
        }
        if (!$request->has('id')) {
            $rules['password'] = [
                'required',
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
            $user->user_type = 2;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->save();
            if($user){
               $society_member = New SocietyMember();
               $society_member->user_id = $user->user_id;
               $society_member->parent_society_member_id = 0;
               $society_member->society_id = $request->society_id;
               $society_member->block_flat_id  = $request->flat_id ;
               $society_member->resident_designation_id  = $request->designation;
               $society_member->resident_type  = $request->resident_type;
               $society_member->created_by = Auth::user()->user_id;
               $society_member->updated_by = Auth::user()->user_id;
               $society_member->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
               $society_member->save();
            }

           
            return response()->json(['status' => '200', 'action' => 'add']);
        }else{
            $user = User::find($request->id);
            if ($user) {
                $user->full_name = $request->full_name;
                $user->email = $request->email;
                $user->mobile_no = $request->mobile_no;
                $user->gender = $request->gender;
                $user->blood_group = $request->blood_group;
                $user->updated_by = Auth::user()->user_id;
                $user->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                $user->save();
                $society_member = SocietyMember::where('user_id',$request->id)->first();
                if($society_member){
                    $society_member->block_flat_id  = $request->flat_id ;
                    $society_member->resident_designation_id  = $request->designation;
                    $society_member->resident_type  = $request->resident_type;
                    $society_member->updated_by = Auth::user()->user_id;
                    $society_member->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                    $society_member->save();
                }

                
                return response()->json(['status' => '200', 'action' => 'update']);
            }

            return response()->json(['status' => '400']);
        }

    }

    public function edit($id){
        $society_member = SocietyMember::select('user.*','society_member.society_member_id','society_member.resident_type','society_member.resident_designation_id','resident_designation.designation_name','society_member.block_flat_id','block_flat.society_block_id')
        ->leftJoin('user', 'society_member.user_id', '=', 'user.user_id')
        ->leftJoin('resident_designation', 'society_member.resident_designation_id', '=', 'resident_designation.resident_designation_id')
        ->leftJoin('block_flat', 'society_member.block_flat_id', '=', 'block_flat.block_flat_id')
        ->where('society_member.society_member_id', $id)->first();
        return response()->json($society_member);
    }

    public function delete($id){
        $society_member = SocietyMember::find($id);
        if ($society_member){
            $user = User::find($society_member->user_id);
            if ($user) {
                $user->estatus = 3;
                $user->save();
                $user->delete();
            }
            $society_member->estatus = 3;
            $society_member->save();
            $society_member->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $society_member = SocietyMember::find($id);
        $user = User::find($society_member->user_id);
        if ($society_member->estatus==1){
            $society_member->estatus = 2;
            $society_member->save();
            $user->estatus = 2;
            $user->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($society_member->estatus==2){
            $society_member->estatus = 1;
            $society_member->save();
            $user->estatus = 1;
            $user->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $society_members = SocietyMember::whereIn('society_member_id', $ids)->get();
        foreach ($society_members as $society_member) {
            $user = User::find($society_member->user_id);
            if($user){
                $user->estatus = 3;
                $user->save();
                $user->delete();
            }
            $society_member->estatus = 3;
            $society_member->save();
            $society_member->delete();
        }
        return response()->json(['status' => '200']);
    }

    public function getFlat(Request $request)
    {
        $data['flats'] = Flat::where("society_block_id",$request->block_id)->get(["flat_no","block_flat_id"]);
        return response()->json($data);
    }
}
