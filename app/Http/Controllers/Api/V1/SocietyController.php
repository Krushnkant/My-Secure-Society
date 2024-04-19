<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Flat;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocietyController extends BaseController
{
    public function society_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required|min:3',
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $societies = Society::where('society_name', 'LIKE',"%{$request->search_text}%")->where('estatus',1)->orderBy('society_name','ASC')->get();
    
        $society_arr = array();
        foreach ($societies as $society) {
            $temp['society_id'] = $society['society_id'];
            $temp['society'] = $society->society_name;
            array_push($society_arr, $temp);
        }
        return $this->sendResponseWithData($society_arr, "All Society Retrieved Successfully.");
    }

    public function block_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_id' => 'required|exists:society',
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $blocks = Block::where('society_id', $request->society_id)->where('estatus',1)->orderBy('block_name','ASC')->get();
    
        $block_arr = array();
        foreach ($blocks as $block) {
            $temp['block_id'] = $block['society_block_id'];
            $temp['block'] = $block->block_name;
            array_push($block_arr, $temp);
        }
        return $this->sendResponseWithData($block_arr, "All Block Retrieved Successfully.");
    }

    public function flat_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_block_id' => 'required|exists:society_block',
        ]);

        if($validator->fails()){
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $flats = Flat::where('society_block_id', $request->society_block_id)->where('estatus',1)->orderBy('flat_no','ASC')->get();
    
        $flat_arr = array();
        foreach ($flats as $flat) {
            $temp['block_flat_id'] = $flat['block_flat_id'];
            $temp['flat'] = $flat->flat_no;
            array_push($flat_arr, $temp);
        }
        return $this->sendResponseWithData($flat_arr, "All Flat Retrieved Successfully.");
    }
}
