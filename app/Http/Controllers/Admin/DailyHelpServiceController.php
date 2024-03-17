<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DailyHelpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DailyHelpServiceController extends Controller
{
    public function index()
    {
        return view('admin.daily_help_service.list');
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
        $query = DailyHelpService::select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('service_name', 'like', "%".$search."%");
        });

        $orderByName = 'service_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'service_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();


        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'service_name.required' => 'Please provide a service name',
        ];
        if(!isset($request->id)){
            $validator = Validator::make($request->all(), [
                'icon' => 'required|image|mimes:jpeg,png,jpg,gif',
                'service_name' => [
                    'required',
                    'max:50',
                    Rule::unique('daily_help_service', 'service_name')
                        ->whereNull('deleted_at'),
                ],
            ], $messages);
        }else{
            $validator = Validator::make($request->all(), [
                'icon' => 'image|mimes:jpeg,png,jpg,gif',
                'service_name' => [
                    'required',
                    'max:50',
                    Rule::unique('daily_help_service', 'service_name')
                        ->ignore($request->id,'daily_help_service_id')
                        ->whereNull('deleted_at'),
                ],
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $help = new DailyHelpService();
            $help->service_name = $request->service_name;
            if ($request->hasFile('icon')) {
                $help->service_icon = $this->uploadIcon($request);
            }
            $help->created_by = Auth::user()->user_id;
            $help->updated_by = Auth::user()->user_id;
            $help->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $help->save();


            return response()->json(['status' => '200', 'action' => 'add']);
        }else{
            $help = DailyHelpService::find($request->id);
            if ($help) {
                $old_image = $help->service_icon;
                $help->service_name = $request->service_name;
                if ($request->hasFile('icon')) {
                    $help->service_icon = $this->uploadIcon($request,$old_image);
                }
                $help->updated_by = Auth::user()->user_id;
                $help->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                $help->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }

            return response()->json(['status' => '400']);
        }

    }


    public function uploadIcon($request,$old_image=""){
        $image = $request->file('icon');
        $image_name = 'icon_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('images/service_icon');
        $image->move($destinationPath, $image_name);
        if(isset($old_image) && $old_image != "") {
            $old_image = public_path($old_image);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        return  'images/service_icon/'.$image_name;
    }

    public function edit($id){
        $help = DailyHelpService::find($id);
        return response()->json($help);
    }

    public function delete($id){
        $help = DailyHelpService::find($id);
        if ($help){
            $help->estatus = 3;
            $help->save();
            $help->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $help = DailyHelpService::find($id);
        if ($help->estatus==1){
            $help->estatus = 2;
            $help->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($help->estatus==2){
            $help->estatus = 1;
            $help->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $helps = DailyHelpService::whereIn('daily_help_service_id', $ids)->get();
        foreach ($helps as $help) {
            $help->estatus = 3;
            $help->save();
        }
        DailyHelpService::whereIn('daily_help_service_id', $ids)->delete();
        return response()->json(['status' => '200']);
    }
}
