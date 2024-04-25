<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DocumentFolderController extends BaseController
{
    public function save_folder(Request $request)
    {
        $rules = [
            'folder_name' => 'required|max:70',
        ];
       
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->folder_id == 0){
            $folder = New DocumentFolder();
            $folder->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $folder->created_by = Auth::user()->user_id;
            $folder->updated_by = Auth::user()->user_id;
            $action = "Added";
        }else{
            $folder = DocumentFolder::find($request->folder_id);
            if (!$folder)
            {
                return $this->sendError(404,'Folder Not Exist.', "Not Found Error", []);
            }
            $folder->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $folder->folder_name = $request->folder_name;
        $folder->save();

        return $this->sendResponseSuccess("Folder ". $action ." Successfully");
    }

  
    public function folder_list()
    {
        $user_id =  Auth::user()->user_id;
        $folders = DocumentFolder::where('estatus',1)->where('created_by',$user_id)->paginate(10);
        $folder_arr = array();
        foreach ($folders as $folder) {
            $temp['document_folder_id'] = $folder->document_folder_id;
            $temp['folder_name'] = $folder->folder_name;
            array_push($folder_arr, $temp);
        }

        $data['folders'] = $folder_arr;
        $data['total_records'] = $folders->toArray()['total'];
        return $this->sendResponseWithData($data, "All Folder Retrieved Successfully.");
    }

    public function delete_folder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_folder_id' => 'required|exists:document_folder,document_folder_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $folder = DocumentFolder::find($request->document_folder_id);
        if ($folder) {
            $folder->estatus = 3;
            $folder->save();
            $folder->delete();
        }
        return $this->sendResponseSuccess("folder deleted Successfully.");
    }

    public function get_folder(Request $request)
    {
        $user_id =  Auth::user()->user_id;
        $validator = Validator::make($request->all(), [
            'document_folder_id' => 'required|exists:document_folder,document_folder_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }
        $folder = DocumentFolder::where('estatus',1)->where('created_by',$user_id)->where('document_folder_id',$request->document_folder_id)->first();
        if (!$folder){
            return $this->sendError(404,"You can not delete this folder", "Invalid folder", []);
        }
        $data = array();
        $temp['document_folder_id'] = $folder->document_folder_id;
        $temp['full_name'] = $folder->folder_name;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "All Folder Retrieved Successfully.");
    }
}
