<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    public function index($id) {
        $society = Society::find($id);
        return view('admin.society_block.list',compact('society','id'));
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
        $query = Block::select('*');
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('block_name', 'like', "%".$search."%");
        });

        $orderByName = 'block_name';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'block_name';
                break;  
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'block_name.required' =>'Please provide a block name',
        ];

        $validator = Validator::make($request->all(), [
            'block_name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        if(!isset($request->id)){
            $block = new Block();
            $block->society_id = $request->society_id;
            $block->block_name = $request->block_name;
            $block->created_by = Auth::user()->user_id;
            $block->updated_by = Auth::user()->user_id;
            $block->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $block->save();
            
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $block = Block::find($request->id);
            if ($block) {
                $block->block_name = $request->block_name;
                $block->updated_by = Auth::user()->user_id;
                $block->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    public function edit($id){
        $block = Block::find($id);
        return response()->json($block);
    }

    public function delete($id){
        $block = Block::find($id);
        if ($block){
            $block->estatus = 3;
            $block->save();
            $block->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id){
        $block = Block::find($id);
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
        Block::whereIn('society_block_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }
}
