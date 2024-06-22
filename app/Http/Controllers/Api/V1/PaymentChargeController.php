<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentCharge;
use App\Models\SocietyLedgerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentChargeController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_payment_charge(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $request->merge(['society_id'=>$society_id]);
        $rules = [
            'charge_id' => 'required|numeric',
            'is_penalty' => 'required|in:1,2',
            'charge_type' => 'required|in:1,2,4,5,7',
            'charge_name' => 'required|string|max:100',
            'amount_type' => 'required|in:1,2',
            'charge_amount' => 'required|numeric',
        ];
        if ($request->has('charge_id') && $request->input('charge_id') != 0) {
            $rules['charge_id'] .= '|exists:society_charge,society_charge_id,deleted_at,NULL,society_id,'.$society_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if($request->charge_id == 0){
            $charge = New PaymentCharge();
            $charge->society_id = $society_id;
            $charge->created_at = now();
            $charge->created_by = Auth::user()->user_id;
            $charge->updated_by = Auth::user()->user_id;
            $action ="Added";
        }else{
            $charge = PaymentCharge::find($request->charge_id);
            if($request->calling_by == 1 &&  $charge->created_by != auth()->id()){
                return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
            }
            $charge->updated_by = Auth::user()->user_id;
            $action ="Updated";
        }

        $charge->charge_name = $request->charge_name;
        $charge->is_penalty = $request->is_penalty;
        $charge->charge_type = $request->charge_type;
        $charge->amount_type = $request->amount_type;
        $charge->charge_amount = $request->charge_amount;
        $charge->save();

        $data = array();
        $temp['charge_id'] = $charge->society_charge_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Society Charge ".$action." Successfully");
    }

    public function payment_charge_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $charges = PaymentCharge::where('society_id', $society_id)->where('estatus', 1)->paginate(10);
        $charge_arr = array();
        foreach ($charges as $charge) {
            $temp['charge_id'] = $charge->society_charge_id;
            $temp['charge_name'] = $charge->charge_name;
            $temp['charge_type'] = $charge->charge_type;
            $temp['is_penalty'] = $charge->is_penalty;
            $temp['amount_type'] = $charge->amount_type;
            $temp['charge_amount'] = $charge->charge_amount;
            $temp['updated_time'] = $charge->updated_at->format('d-m-Y H:i:s');
            array_push($charge_arr, $temp);
        }

        $data['charges_list'] = $charge_arr;
        $data['total_records'] = $charges->toArray()['total'];
        return $this->sendResponseWithData($data, "All Payment Charge Successfully.");
    }

    public function get_payment_charge(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'charge_id' => 'required|exists:society_charge,society_charge_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $charge = PaymentCharge::find($request->charge_id);

        if (!$charge) {
            return response()->json(['error' => 'charge not found'], 404);
        }

        $data = array();
        $temp['charge_id'] = $charge->society_charge_id;
        $temp['charge_name'] = $charge->charge_name;
        $temp['charge_type'] = $charge->charge_type;
        $temp['is_penalty'] = $charge->is_penalty;
        $temp['amount_type'] = $charge->amount_type;
        $temp['charge_amount'] = $charge->charge_amount;
        array_push($data, $temp);

        return $this->sendResponseWithData($data, " Society Charge Detail Retrieved Successfully.");
    }

    public function delete_staff_member_duty_area(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'charge_id' => 'required|exists:society_charge,society_charge_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $charge = PaymentCharge::find($request->duty_area_time_id);
        if(getResidentDesignation($this->payload['designation_id']) == "Society Member" &&  $charge->created_by != auth()->id()){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }
        $charge->estatus = 3;
        $charge->save();
        $charge->delete();

        return $this->sendResponseSuccess("society charge deleted successfully.");
    }

    public function get_payment_society_ledger(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $data = array();
        $ledger = SocietyLedgerDetail::where('estatus',1)->where('society_id',$society_id)->first();

        if (!$ledger){
            $temp['total_balance_amount'] = 0;
            $temp['current_balance_amount'] = 0;
            $temp['disbursed_loan_amount'] = 0;
        }else{
            $temp['total_balance_amount'] = $ledger->total_balance;
            $temp['current_balance_amount'] = $ledger->current_balance;
            $temp['disbursed_loan_amount'] = $ledger->current_disbursed_loan_amount;
        }
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Payment Society Ledger Successfully.");
    }
}
