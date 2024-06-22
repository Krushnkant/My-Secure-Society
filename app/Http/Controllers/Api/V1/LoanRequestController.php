<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\SocietyLedgerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanRequestController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function create_loan_request(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'requested_amount' => 'required',
            'description' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $loan = new LoanRequest();
            $loan->loan_no = generateLoanRequestNumber($society_id);
            $loan->society_id = $society_id;
            $loan->block_flat_id = $this->payload['block_flat_id'];
            $loan->requested_loan_amount = $request->requested_amount;
            $loan->loan_amount = 0;
            $loan->paid_amount = 0;
            $loan->outstanding_amount = 0;
            $loan->interest_rate = 0;
            $loan->loan_description = $request->description;
            $loan->loan_status = 3;
            $loan->created_by = Auth::user()->user_id;
            $loan->updated_by = Auth::user()->user_id;
            $loan->save();

            DB::commit();
            $data = array();
            $temp['loan_request_id'] = $loan->loan_request_id;
            array_push($data, $temp);
            return $this->sendResponseWithData($data, "loan request Successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while updating the loan request.', "Internal Server Error", []);
        }
    }

    public function loan_request_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'user_id' => 'required|integer',
            'request_status' => 'required|integer|in:1,2,3,4',
        ];

        if ($request->has('user_id') && $request->input('user_id') != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $query = LoanRequest::query();

        if ($request->input('user_id') != 0) {
            $query->where('invoice_to_user_id', $request->input('user_id'));
        }

        if ($request->input('request_status') != 0) {
            $query->where('loan_status', $request->input('request_status'));
        }

        $loans = $query->orderBy('created_at', 'DESC')->paginate(10);

        $loan_arr = [];
        foreach ($loans as $loan) {
            $temp = [];
            $temp['loan_request_id'] = $loan->loan_request_id;
            $temp['requested_amount'] = $loan->requested_loan_amount;
            $temp['loan_amount'] = $loan->loan_amount;
            $temp['outstand_loan_amount'] = $loan->outstanding_amount;
            $temp['request_status'] = $loan->loan_status;
            $temp['created_date'] = $loan->created_at->format('d-m-Y');
            array_push($loan_arr, $temp);
        }

        $data['request_list'] = $loan_arr;
        $data['total_records'] = $loans->total();

        return $this->sendResponseWithData($data, "invoice list retrieved successfully.");
    }

    public function get_loan_request(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'loan_request_id' => 'required|integer|exists:loan_request,loan_request_id,deleted_at,NULL',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $loan = LoanRequest::where('loan_request_id', $request->input('loan_request_id'))->first();

        if (!$loan) {
            return $this->sendError(404, 'loan Not Found.', "Not Found", []);
        }
        $loan_arr = [];

        $temp['loan_request_id'] = $loan->loan_request_id;
        $temp['requested_amount'] = $loan->requested_loan_amount;
        $temp['loan_amount'] = $loan->loan_amount;
        $temp['outstand_loan_amount'] = $loan->outstanding_amount;
        $temp['description'] = $loan->loan_description;
        $temp['approval_description'] = $loan->approval_description;
        $temp['request_status'] = $loan->loan_status;
        $temp['created_date'] = $loan->created_at->format('d-m-Y');
        array_push($loan_arr, $temp);

        return $this->sendResponseWithData($loan_arr, "invoice retrieved successfully.");
    }

    public function loan_request_change_status(Request $request)
    {
        $interest_rate = 10;
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }


        $rules = [
            'loan_request_id' => 'required|exists:loan_request,loan_request_id,deleted_at,NULL,society_id,'.$society_id,
            'request_status' => 'required|in:1,2',
        ];

        if ($request->has('request_status') && $request->input('request_status') == 1) {
            $rules['loan_amount'] = 'required|numeric|min:1';
        }

        $validator = Validator::make($request->all(), $rules);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $loan = LoanRequest::where('loan_request_id', $request->loan_request_id)->firstOrFail();
        if ($loan->loan_status == $request->request_status) {
            return $this->sendError(400, "You can't Update the Status, The loan is already in the requested status.", "Bad Request", []);
        }
        $loan->loan_status = $request->request_status;
        $loan->approval_description = $request->description;
        $loan->outstanding_amount = $request->loan_amount??0;
        $loan->loan_amount = $request->loan_amount??0;
        $loan->interest_rate = $interest_rate;
        $loan->save();
        if($request->request_status == 1){
            $ledger = SocietyLedgerDetail::where('society_id',$society_id)->first();
            $ledger->current_disbursed_loan_amount = $ledger->current_disbursed_loan_amount  + $request->loan_amount;
            $ledger->current_balance = $ledger->current_balance - $request->loan_amount;
            $ledger->save();
        }

        return $this->sendResponseSuccess("Status Updated Successfully.");
    }
}
