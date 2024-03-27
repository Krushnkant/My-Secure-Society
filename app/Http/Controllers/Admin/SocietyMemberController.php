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

      
        $query = SocietyMember::select('user.*')
        ->leftJoin('user', 'society_member.user_id', '=', 'user.user_id')
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

                
                return response()->json(['status' => '200', 'action' => 'update']);
            }

            return response()->json(['status' => '400']);
        }

    }

    public function edit($id){
        $block = SocietyMember::find($id);
        return response()->json($block);
    }

    public function delete($id){
        $block = SocietyMember::find($id);
        if ($block){
            $flat = Flat::where('society_block_id', $id)->exists();
            if ($flat) {
                return response()->json(['status' => '300', 'message' => 'Cannot delete society. It is associated with one or more society block.']);
            }
            $block->estatus = 3;
            $block->save();
            $block->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $block = SocietyMember::find($id);
        if ($block->estatus==1){
            $block->estatus = 2;
            $block->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($block->estatus==2){
            $block->estatus = 1;
            $block->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $blocks = SocietyMember::whereIn('society_block_id', $ids)->get();
        foreach ($blocks as $block) {
            $flat = Flat::where('society_block_id', $block->society_block_id)->exists();
            if (!$flat) {
                $block->estatus = 3;
                $block->save();
                $block->delete();
            }
        }
        return response()->json(['status' => '200']);
    }

    public function getFlat(Request $request)
    {
        $data['flats'] = Flat::where("society_block_id",$request->block_id)->get(["flat_no","block_flat_id"]);
        return response()->json($data);
    }
}
