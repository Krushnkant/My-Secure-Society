<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyDocument;
use App\Models\SocietyDocumentFile;
use App\Models\DocumentSharedFlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocietyDocumentController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_document(Request $request)
    {
        $user_id = Auth::id();
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society ID not provided.', "Not Found", []);
        }

        $rules = [
            'document_id' => 'required',
            'folder_id' => 'required', // Validate existence in document_folder table
            'document_type' => 'required',
            'document_name' => 'required|max:100',
            'document_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048', // Validate file upload
            'shared_flat_list' => 'array', // Ensure shared_flat_list is an array
            'shared_flat_list.*' => 'exists:block_flat,block_flat_id',
        ];

        // Custom error messages for validation rules
        $messages = [
            'shared_flat_list.*.exists' => 'The selected flat is not exist.',
        ];

        if ($request->input('folder_id') != 0) {
            $rules['folder_id'] .= '|exists:document_folder,document_folder_id,deleted_at,NULL,created_by,'.$user_id;
        }

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->document_id == 0){
            $document = New SocietyDocument();
            $document->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $document->created_by = Auth::user()->user_id;
            $document->updated_by = Auth::user()->user_id;
            $action = "Added";
        }else{
            $document = SocietyDocument::find($request->document_id);
            $document->updated_by = Auth::user()->user_id;
            $action = "Updated";
        }
        $document->society_id = $society_id;
        $document->document_folder_id = $request->folder_id;
        $document->document_type  = $request->document_type;
        $document->document_name  = $request->document_name;
        $document->note  = $request->note;
        $document->save();

        if($document){
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $fileUrl = UploadImage($file,'images/society_document');
                $fileType = getFileType($file);
                $doc_file = new SocietyDocumentFile();
                $doc_file->society_document_id = $document->society_document_id;
                $doc_file->file_type = $fileType;
                $doc_file->file_url = $fileUrl;
                $doc_file->uploaded_at = now();
                $doc_file->save();
            }

            if (isset($request->shared_flat_list)) {
                foreach($request->shared_flat_list as $share_flat){
                    $shared_flat = new DocumentSharedFlat();
                    $shared_flat->society_document_id = $document->society_document_id;
                    $shared_flat->block_flat_id = $share_flat;
                    $shared_flat->updated_at = now();
                    $shared_flat->updated_by = Auth::user()->user_id;
                    $shared_flat->save();
                }
            }

        }

        $data = array();
        $temp['document_id'] = $document->society_document_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Document ". $action ." Successfully");
    }


    public function document_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $user_id = Auth::id();
        $block_flat_id = $this->payload['block_flat_id'];
        if (empty($block_flat_id)) {
            return $this->sendError(400, 'Block Flat ID not provided.', "Not Found", []);
        }

        $rules = [
            'document_type' => 'required',
            'folder_id' => 'required',
        ];

        if($request->folder_id > 0){
            $rules = [
                'folder_id' => ' |exists:document_folder,document_folder_id,deleted_at,NULL',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $query = SocietyDocument::where('estatus', 1)
        ->where('society_id', $society_id)
        ->where('document_folder_id', $request->folder_id)
        ->where(function ($query) use ($user_id, $block_flat_id) {
            $query->where('created_by', $user_id)
                ->orWhereHas('sharedocumentflat', function ($query) use ($block_flat_id) {
                    $query->where('block_flat_id', $block_flat_id);
                });
        })
        ->with('sharedocumentflat', 'document_file')
        ->orderBy('created_at', 'DESC');

        // Apply documentType filter if provided
        if ($request->document_type > 0) {
            $query->where('document_type', $request->document_type);
        }

        $query->orderBy('created_at', 'DESC');
        $perPage = 10;
        $documents = $query->paginate($perPage);

        $document_arr = array();
        foreach ($documents as $document) {
            $tempfile = [];
            if(isset($document->document_file)){
                $tempfile['society_document_file_id'] = $document->document_file->society_document_file_id;
                $tempfile['file_type'] = $document->document_file->file_type;
                $tempfile['file_url'] = url($document->document_file->file_url);
            }
            $temp['document_id'] = $document->society_document_id;
            $temp['folder_id'] = $document->document_folder_id;
            $temp['document_type'] = $document->document_type;
            $temp['document_name'] = $document->document_name;
            $temp['document_file'] = $tempfile;
            $temp['note'] = $document->note;
            $temp['is_shared'] = ($user_id == $document->created_by) ? False : True;
            array_push($document_arr, $temp);
        }

        $data['document_list'] = $document_arr;
        $data['total_records'] = $documents->toArray()['total'];
        return $this->sendResponseWithData($data, "All Document Retrieved Successfully.");
    }

    public function delete_document(Request $request)
    {
        $user_id = Auth::id();
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|exists:society_document,society_document_id,deleted_at,NULL,created_by,'.$user_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $folder = SocietyDocument::find($request->document_id);
        if ($folder) {
            $shares = DocumentSharedFlat::where('society_document_id',$request->document_id)->get();
            foreach($shares as $share){
                $share->delete();
            }
            $folder->estatus = 3;
            $folder->save();
            $folder->delete();
        }
        return $this->sendResponseSuccess("document deleted Successfully.");
    }

    public function get_document(Request $request)
    {

        $user_id = Auth::id();
        $block_flat_id = $this->payload['block_flat_id'];
        if (empty($block_flat_id)) {
            return $this->sendError(400, 'Block Flat ID not provided.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'document_id' => 'required|exists:society_document,society_document_id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $userdocument = SocietyDocument::where('society_document_id',$request->document_id)
        // ->where(function ($query) use ($user_id, $block_flat_id) {
        //     $query->where('created_by', $user_id)
        //         ->orWhereHas('sharedocumentflat', function ($query) use ($block_flat_id) {
        //             $query->where('block_flat_id', $block_flat_id);
        //         });
        // })
        ->where(function ($query) use ($user_id, $block_flat_id) {
            $query->where('created_by', $user_id)
                ->orWhereHas('sharedocumentflat', function ($query) use ($block_flat_id) {
                    $query->where('block_flat_id', $block_flat_id);
                });
        })
        ->with('sharedocumentflat')->first();
        if (!$userdocument){
            return $this->sendError(404,"You can not view this document", "Invalid document", []);
        }
        $document = SocietyDocument::with('document_file')->where('estatus',1)->where('society_document_id',$request->document_id)->first();
        if (!$document){
            return $this->sendError(404,"You can not view this document", "Invalid document", []);
        }
        $data = array();
        $tempfile = [];
        if(isset($document->document_file)){
            $tempfile['society_document_file_id'] = $document->document_file->society_document_file_id;
            $tempfile['file_type'] = $document->document_file->file_type;
            $tempfile['file_url'] = url($document->document_file->file_url);
        }

        $flatshareArray = [];
        if(isset($userdocument->sharedocumentflat)){
            foreach($userdocument->sharedocumentflat as $sharedocumentflat){
                $flat_info = getSocietyBlockAndFlatInfo($sharedocumentflat->block_flat_id);
                $flatshare['document_shared_flat_id'] = $sharedocumentflat->document_shared_flat_id;
                $flatshare['block_flat_id'] = $sharedocumentflat->block_flat_id;
                $flatshare['block_id'] = $flat_info['block_id'];
                array_push($flatshareArray, $flatshare);
            }
        }

        $temp['document_id'] = $document->society_document_id;
        $temp['folder_id'] = $document->document_folder_id;
        $temp['document_type'] = $document->document_type;
        $temp['document_name'] = $document->document_name;
        $temp['document_file'] = $tempfile;
        $temp['note'] = $document->note;
        $temp['shared_flat_list'] = $flatshareArray;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "All Document Retrieved Successfully.");
    }
}
