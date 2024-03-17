<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function index()
    {
        return view('admin.emergency_contact.list');
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
        $query = EmergencyContact::select('*')->where('contact_type',1);
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('name', 'like', "%".$search."%");
        });

        $orderByName = 'name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'name.required' =>'Please provide a name',
            'mobile_no.required' =>'Please provide a mobile number',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'mobile_no' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $emergencycontact = new EmergencyContact();
            $emergencycontact->name = $request->name;
            $emergencycontact->mobile_no = $request->mobile_no;
            $emergencycontact->contact_type = 1;
            $emergencycontact->master_id = 0;
            $emergencycontact->created_by = Auth::user()->user_id;
            $emergencycontact->updated_by = Auth::user()->user_id;
            $emergencycontact->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $emergencycontact->save();

            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $emergencycontact = EmergencyContact::find($request->id);
            if ($emergencycontact) {
                $emergencycontact->name = $request->name;
                $emergencycontact->mobile_no = $request->mobile_no;
                $emergencycontact->updated_by = Auth::user()->user_id;
                $emergencycontact->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }


    public function edit($id){
        $emergencycontact = EmergencyContact::find($id);
        return response()->json($emergencycontact);
    }

    public function delete($id){
        $emergencycontact = EmergencyContact::find($id);
        if ($emergencycontact){
            $emergencycontact->estatus = 3;
            $emergencycontact->save();
            $emergencycontact->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $emergencycontact = EmergencyContact::find($id);
        if ($emergencycontact->estatus==1){
            $emergencycontact->estatus = 2;
            $emergencycontact->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($emergencycontact->estatus==2){
            $emergencycontact->estatus = 1;
            $emergencycontact->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $contacts = EmergencyContact::whereIn('emergency_contact_id', $ids)->get();
        foreach ($contacts as $contact) {
            $contact->estatus = 3;
            $contact->save();
        }
        EmergencyContact::whereIn('emergency_contact_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }


}
