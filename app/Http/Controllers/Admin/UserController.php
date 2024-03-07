<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.list');
    }
    
    public function listdata(Request $request){
      
        // Page Length
        $pageNumber = ( $request->start / $request->length )+1;
        $pageLength = $request->length;
        $skip       = ($pageNumber-1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = User::select('*')->where('user_type','!=',1);
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('full_name', 'like', "%".$search."%");
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

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'full_name.required' =>'Please provide a full name',
        ];

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $user = new Designation();
            $user->full_name = $request->full_name;
            $user->created_by = Auth::user()->user_id;
            $user->updated_by = Auth::user()->user_id;
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->save();

            $this->defalt_permission($user->user_id);
            
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $user = User::find($request->id);
            if ($user) {
                $user->full_name = $request->full_name;
                $user->updated_by = Auth::user()->user_id;
                $user->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    public function edit($id){
        $user = User::find($id);
        return response()->json($user);
    }

    public function delete($id){
        $user = User::find($id);
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
        User::whereIn('user_id', $ids)->delete();
        return response()->json(['status' => '200']);
    }
}
