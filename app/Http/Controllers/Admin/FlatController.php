<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FlatController extends Controller
{
    public function index($id) {
        $block = Block::with('society')->find($id);
        if($block == null){
            return view('admin.404');
        }
        return view('admin.block_flat.list',compact('block','id'));
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
        $query = Flat::select('*')->where('society_block_id',$request->block_id);
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('flat_no', 'like', "%".$search."%");
        });

        $orderByName = 'flat_no';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'flat_no';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'flat_no.required' =>'Please provide a flat no',
            'flat_no.unique' => 'The flat no is already taken for this block',
        ];
        if ($request->has('id') && $request->has('flat_no')) {
            $rules['flat_no'] = [
                'required',
                'integer',
                Rule::unique('block_flat')->where(function ($query) use ($request) {
                    return $query->where('society_block_id', $request->society_block_id)->where('block_flat_id', '!=', $request->id)->whereNull('deleted_at');
                }),
            ];
        } elseif ($request->has('flat_no')) {
            $rules['flat_no'] = [
                'required',
                'integer',
                Rule::unique('block_flat')->where(function ($query) use ($request) {
                    return $query->where('society_block_id', $request->society_block_id)->whereNull('deleted_at');
                }),
            ];
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $flat = new Flat();
        }
        else{
            $flat = Flat::find($request->id);
            if (!$flat) {
                return response()->json(['status' => '400']);
            }
        }
        $flat->society_block_id = $request->society_block_id;
        $flat->flat_no = $request->flat_no;
        //$flat->is_empty = $request->is_empty;
        $flat->created_by = Auth::user()->user_id;
        $flat->updated_by = Auth::user()->user_id;
        $flat->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $flat->save();
        return response()->json(['status' => '200', 'action' => 'add']);
    }

    public function edit($id){
        $flat = Flat::find($id);
        return response()->json($flat);
    }

    public function delete($id){
        $flat = Flat::find($id);
        if ($flat){
            $flat->estatus = 3;
            $flat->save();
            $flat->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $flat = Flat::find($id);
        if ($flat->estatus==1){
            $flat->estatus = 2;
            $flat->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($flat->estatus==2){
            $flat->estatus = 1;
            $flat->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $flats = Flat::whereIn('block_flat_id', $ids)->get();
        foreach ($flats as $flat) {
            $flat->estatus = 3;
            $flat->save();
        }
        Flat::whereIn('block_flat_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }
}
