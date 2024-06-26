<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocietyMember;
use App\Models\Flat;
use App\Models\ResidentDesignation;
use App\Models\SocietyVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SocietyMemberController extends BaseController
{
    public function save_flat(Request $request)
    {
        
        $user_id = Auth::id();
        $validator = Validator::make($request->all(), [
            'society_id' => 'required|exists:society,society_id,deleted_at,NULL',
            'block_flat_id' => 'required|exists:block_flat,block_flat_id,deleted_at,NULL',
            'resident_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $societyId = $request->input('society_id');
        $blockFlat = Flat::with('society_block')->find($request->block_flat_id); // Ensure the relationship is eager loaded

        if (!$blockFlat || $blockFlat->society_block->society_id != $societyId) {
            return $this->sendError(422, 'The selected Flat is not associated with the provided Society.', "Validation Errors", []);
        }
    
        $existingAssociation = SocietyMember::where('user_id', $user_id)
            ->where('block_flat_id', $request->block_flat_id)
            ->exists();

        if ($existingAssociation) {
            return $this->sendError(422, 'User is already associated with this flat.', "Validation Errors", []);
        }

        $designation = ResidentDesignation::select('resident_designation_id')->where('society_id', $societyId)->where('designation_name', 'Society Member')->first();
        $society_member = new SocietyMember();
        $society_member->user_id = $user_id;
        $society_member->parent_society_member_id  = 0;
        $society_member->society_id = $request->society_id;
        $society_member->resident_designation_id = $designation->resident_designation_id;
        $society_member->block_flat_id = $request->block_flat_id;
        $society_member->resident_type = $request->resident_type;
        $society_member->estatus  = 4;
        $society_member->created_by = Auth::id();
        $society_member->updated_by = Auth::id();
        $society_member->save();

        $data = array();
        $temp['society_member_id'] = $society_member->society_member_id;
        $temp['request_status'] = $society_member->estatus;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Flat Added Successfully.");
    }

    public function flat_list(Request $request)
    {
        $user_id = Auth::id();
        $society_members = SocietyMember::where('user_id', $user_id);
        $society_members = $society_members->orderBy('created_at', 'DESC')->paginate(10);

        $society_member_arr = array();
        foreach ($society_members as $society_member) {
            $flat_info = getSocietyBlockAndFlatInfo($society_member['block_flat_id']);
            $temp['society_member_id'] = $society_member['society_member_id'];
            $temp['block_flat_id'] = $society_member['block_flat_id'];
            $temp['flat_no'] = $flat_info['flat_no'];
            $temp['block_name'] = $flat_info['block_name'];
            $temp['society_name'] = $flat_info['society_name'];
            $temp['request_status'] = $society_member['estatus'];
            array_push($society_member_arr, $temp);
        }

        $data['flat_list'] = $society_member_arr;
        $data['total_records'] = $society_members->toArray()['total'];
        return $this->sendResponseWithData($data, "All Flats Retrieved Successfully.");
    }

    public function delete_flat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'society_member_id' => 'required|exists:society_member,society_member_id,user_id,' . auth()->id().',deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $society_member = SocietyMember::find($request->society_member_id);
        if ($society_member) {
            $family_members = SocietyMember::where('parent_society_member_id',$request->society_member_id)->get();
            foreach($family_members as $family_member){
                $family_member->estatus = 3;
                $family_member->save();
                $family_member->delete();
            }

            $society_visitors = SocietyVisitor::where('block_flat_id',$society_member->block_flat_id)->where('visitor_status',3)->get();
            foreach($society_visitors as $society_visitor){
                $society_visitor->estatus = 3;
                $society_visitor->save();
            }
            $society_member->estatus = 3;
            $society_member->save();
            $society_member->delete();
        }
        return $this->sendResponseSuccess("flat deleted Successfully.");
    }
}
