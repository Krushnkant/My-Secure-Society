<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\SocietyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class SocietyDocumentController extends Controller
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_document(Request $request)
    {

        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError('Society Not Found.', "Not Found", []);
        }

        $rules = [
            'society_document_id' => 'required',
            'document_folder_id' => 'required|exists:document_folder',
            'document_type' => 'required',
            'document_name' => 'required',
            'document_file' => 'required',
        ];
       
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        if($request->society_document_id == 0){
            $folder = New SocietyDocument();
            $folder->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $folder->created_by = Auth::user()->user_id;
            $folder->updated_by = Auth::user()->user_id;
            $action = "Added";
        }else{
            $folder = SocietyDocument::find($request->society_document_id);
            $folder->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $folder->society_id = $society_id;
        $folder->document_folder_id = $request->document_folder_id;
        $folder->document_type  = $request->document_type;
        $folder->document_name  = $request->document_name;
        $folder->note  = $request->note;
        $folder->save();

        return $this->sendResponseSuccess("Folder ". $action ." Successfully");
    }

  
    public function document_list()
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError('Society Not Found.', "Not Found", []);
        }

        $documents = SocietyDocument::where('estatus',1)->where('society_id',$society_id)->paginate(10);
        $document_arr = array();
        foreach ($documents as $document) {
            $temp['society_document_id'] = $document->society_document_id;
            $temp['document_folder_id'] = $document->document_folder_id;
            $temp['document_type'] = $document->document_type;
            $temp['document_name'] = $document->document_name;
            $temp['note'] = $document->note;
            array_push($document_arr, $temp);
        }

        $data['documents'] = $document_arr;
        $data['total_records'] = $documents->toArray()['total'];
        return $this->sendResponseWithData($data, "All Document Retrieved Successfully.");
    }

    public function delete_document(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_document_id' => 'required|exists:society_document',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $folder = SocietyDocument::find($request->society_document_id);
        if ($folder) {
            $folder->estatus = 3;
            $folder->save();
            $folder->delete();
        }
        return $this->sendResponseSuccess("folder deleted Successfully.");
    }

    public function get_document(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_document_id' => 'required|exists:society_document',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        $document = SocietyDocument::where('estatus',1)->first();
        if (!$document){
            return $this->sendError("You can not view this document", "Invalid document", []);
        }
        $data = array();
        $temp['society_document_id'] = $document->society_document_id;
        $temp['document_folder_id'] = $document->document_folder_id;
        $temp['document_type'] = $document->document_type;
        $temp['document_name'] = $document->document_name;
        $temp['note'] = $document->note;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "All Folder Retrieved Successfully.");
    }
}
