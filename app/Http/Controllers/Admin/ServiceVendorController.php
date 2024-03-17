<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceVendor;
use App\Models\ServiceVendorFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceVendorController extends Controller
{
    public function index()
    {
        return view('admin.service_vendor.list');
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
        $query = ServiceVendor::with('service_vendor_file')->select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('vendor_company_name', 'like', "%".$search."%");
        });

        $orderByName = 'vendor_company_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'vendor_company_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();


        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'file.required' =>'Please provide a logo',
            'file.image' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'file.mimes' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'vendor_company_name.required' => 'Please provide a vendor',
        ];
        if(!isset($request->id)){
            $validator = Validator::make($request->all(), [
                'file' => 'required|image|mimes:jpeg,png,jpg',
                'vendor_company_name' => [
                    'required',
                    'max:50',
                    Rule::unique('service_vendor', 'vendor_company_name')
                        ->whereNull('deleted_at'),
                ],

            ], $messages);
        }else{
            $validator = Validator::make($request->all(), [
                'file' => 'image|mimes:jpeg,png,jpg',
                'vendor_company_name' => [
                    'required',
                    'max:50',
                    Rule::unique('service_vendor', 'vendor_company_name')
                        ->ignore($request->id,'service_vendor_id')
                        ->whereNull('deleted_at'),
                ],

            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $service = new ServiceVendor();
            $service->vendor_company_name = $request->vendor_company_name;
            $service->service_type = $request->service_type;
            $service->created_by = Auth::user()->user_id;
            $service->updated_by = Auth::user()->user_id;
            $service->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $service->save();

            if($service){
                if ($request->hasFile('file')) {
                    $servicefile = new ServiceVendorFile();
                    $servicefile->service_vendor_id = $service->service_vendor_id;
                    $servicefile->file_type  = 1;
                    $servicefile->file_url = $this->uploadFile($request);
                    $service->uploaded_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                    $servicefile->save();
                }
            }
            return response()->json(['status' => '200', 'action' => 'add']);
        }else{
            $service = ServiceVendor::find($request->id);
            if ($service) {

                $service->vendor_company_name = $request->vendor_company_name;
                $service->service_type = $request->service_type;
                $service->updated_by = Auth::user()->user_id;
                $service->updated_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                $service->save();

                if($service){
                    if ($request->hasFile('file')) {
                        $servicefile = ServiceVendorFile::where('service_vendor_id',$service->service_vendor_id)->first();
                        if(!$servicefile){
                            $servicefile = new ServiceVendorFile();
                            $servicefile->service_vendor_id = $service->service_vendor_id;
                            $servicefile->file_type  = 1;
                        }else{
                            $old_image = $servicefile->file_url;
                        }

                        $servicefile->file_url = $this->uploadFile($request,$old_image);
                        $service->uploaded_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                        $servicefile->save();
                    }
                }
                return response()->json(['status' => '200', 'action' => 'update']);
            }

            return response()->json(['status' => '400']);
        }

    }

    public function uploadFile($request,$old_image=""){
        $image = $request->file('file');
        $image_name = 'file_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('images/sercice_vendor_file');
        $image->move($destinationPath, $image_name);
        if(isset($old_image) && $old_image != "") {
            $old_image = public_path($old_image);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        return  'images/sercice_vendor_file/'.$image_name;
    }


    public function edit($id){
        $service = ServiceVendor::with('service_vendor_file')->find($id);
        return response()->json($service);
    }

    public function delete($id){
        $service = ServiceVendor::find($id);
        if ($service){
            $service->estatus = 3;
            $service->save();
            $service->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $service = ServiceVendor::find($id);
        if ($service->estatus==1){
            $service->estatus = 2;
            $service->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($service->estatus==2){
            $service->estatus = 1;
            $service->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $services = ServiceVendor::whereIn('service_vendor_id', $ids)->get();
        foreach ($services as $service) {
            $service->estatus = 3;
            $service->save();
        }
        ServiceVendor::whereIn('service_vendor_id', $ids)->delete();
        return response()->json(['status' => '200']);
    }
}
