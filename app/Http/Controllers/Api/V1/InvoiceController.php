<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use App\Models\SocietyLedgerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function create_invoice(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'block_flat_id' => 'required|integer|exists:block_flat,block_flat_id,deleted_at,NULL',
            'invoice_type' => 'required|integer|in:1,2,3,4,5,6,7',
            'user_type' => 'required|integer|in:1,2,3,4',
            'invoice_to_user_id' => 'required|integer',
            'invoice_user_name' => 'required',
            'invoice_amount' => 'required|numeric|min:0',
            'invoice_start_date' => 'nullable|date|after_or_equal:today',
            'invoice_end_date' => 'nullable|date|after_or_equal:invoice_start_date',
            'due_date' => 'nullable|date|after_or_equal:today',
            //'gateway_name' => 'required|string|max:100',
            'payment_status' => 'required|integer|in:1,2',
            'payment_mode' => 'required_if:payment_status,1|integer|in:1,2',
            'list_of_items' => 'required|array',
            'list_of_items.*.charge_id' => 'required|integer',
            'list_of_items.*.description' => 'required|string|max:255',
            'list_of_items.*.charge_amount' => 'required|numeric|min:0',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $invoice = new Invoice();
            $invoice->invoice_type = $request->invoice_type;
            $invoice->invoice_to_user_type = $request->user_type;
            $invoice->block_flat_id = $request->block_flat_id;
            $invoice->invoice_to_user_id = $request->invoice_to_user_id;
            $invoice->invoice_user_name = $request->invoice_user_name;
            $invoice->invoice_no = generateInvoiceNumber($society_id);// You may have a function to generate invoice numbers
            $invoice->invoice_period_start_date = $request->invoice_start_date;
            $invoice->invoice_period_end_date = $request->invoice_end_date;
            $invoice->due_date = $request->due_date; // You may have a function to calculate due date
            $invoice->invoice_amount = $request->invoice_amount;
            $invoice->created_by = Auth::user()->user_id;
            $invoice->updated_by = Auth::user()->user_id;
            $invoice->save();

            foreach ($request->list_of_items as $item) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->invoice_id;
                $invoiceItem->invoice_item_type = $invoice->invoice_to_user_type;
                $invoiceItem->invoice_item_master_id = $item['charge_id'];
                $invoiceItem->invoice_item_description = $item['description'];
                $invoiceItem->invoice_item_amount = $item['charge_amount'];
                $invoiceItem->updated_by = Auth::user()->user_id;
                $invoiceItem->save();
            }

            if ($request->payment_status == 1) {
                $paymentTransaction = new PaymentTransaction();
                $paymentTransaction->invoice_id = $invoice->invoice_id;
                $paymentTransaction->transaction_no = generateTransactionNumber();
                $paymentTransaction->payment_currency = "INR";
                $paymentTransaction->transaction_amount = $request->invoice_amount;
                $paymentTransaction->gateway_name = $request->gateway_name;
                $paymentTransaction->payment_mode = $request->payment_mode;
                $paymentTransaction->payment_status = $request->payment_status;
                $paymentTransaction->created_by = Auth::user()->user_id;
                $paymentTransaction->updated_by = Auth::user()->user_id;
                $paymentTransaction->save();
                if($request->invoice_type != 7){
                    $ledger = SocietyLedgerDetail::where('society_id',$society_id)->first();
                    $ledger->current_balance = $ledger->current_balance  + $request->invoice_amount;
                    $ledger->total_balance = $ledger->total_balance + $request->invoice_amount;
                    $ledger->save();
                }else{
                    $ledger = SocietyLedgerDetail::where('society_id',$society_id)->first();
                    $ledger->current_balance = $ledger->current_balance  - $request->invoice_amount;
                    $ledger->total_balance = $ledger->total_balance - $request->invoice_amount;
                    $ledger->save();
                }
            }


            DB::commit();
            $data = array();
            $temp['invoice_id'] = $invoice->invoice_id;
            array_push($data, $temp);
            return $this->sendResponseWithData($data, "Invoice Create Successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while updating the invoice.', "Internal Server Error", []);
        }
    }

    public function invoice_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'user_id' => 'required|integer',
            'payment_status' => 'required|integer|in:0,1,2',
            'invoice_type' => 'required|integer|in:0,1,2,3,4,5,6,7',
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d'
        ];

        if ($request->has('user_id') && $request->input('user_id') != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $query = Invoice::with('paymentTransaction');

        if ($request->input('user_id') != 0) {
            $query->where('invoice_to_user_id', $request->input('user_id'));
        }

        if ($request->input('payment_status') != 0) {
            $query->whereHas('paymentTransaction', function($q) use ($request) {
                $q->where('payment_status', $request->input('payment_status'));
            });
        }

        if ($request->input('from_date')) {
            $query->whereDate('invoice_period_start_date', '>=', $request->input('from_date'));
        }

        if ($request->input('to_date')) {
            $query->whereDate('invoice_period_end_date', '<=', $request->input('to_date'));
        }

        $invoices = $query->orderBy('created_at', 'DESC')->paginate(10);

        $invoice_arr = [];
        foreach ($invoices as $invoice) {
            $temp = [];
            $temp['invoice_id'] = $invoice->invoice_id;
            $temp['invoice_amount'] = $invoice->invoice_amount;
            $temp['payment_status'] =  $invoice->paymentTransaction ? $invoice->paymentTransaction->payment_status : 2;
            $temp['due_date'] = $invoice->due_date;
            array_push($invoice_arr, $temp);
        }

        $data['invoice_list'] = $invoice_arr;
        $data['total_records'] = $invoices->total();

        return $this->sendResponseWithData($data, "invoice list retrieved successfully.");
    }

    public function get_invoice(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if (empty($society_id)) {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'invoice_id' => 'required|integer|exists:invoice,invoice_id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $invoice = Invoice::where('invoice_id', $request->input('invoice_id'))->first();

        if (!$invoice) {
            return $this->sendError(404, 'Invoice Not Found.', "Not Found", []);
        }
        $invoice_arr = [];

        $temp['invoice_id'] = $invoice->invoice_id;
        $temp['invoice_type'] = $invoice->invoice_type;
        $temp['block_flat_id'] = $invoice->block_flat_id;
        $temp['invoice_period_start_date'] = $invoice->invoice_period_start_date;
        $temp['invoice_period_end_date'] = $invoice->invoice_period_end_date;
        $temp['due_date'] = $invoice->due_date;
        $temp['invoice_amount'] = $invoice->invoice_amount;
        $temp['payment_status'] =  $invoice->paymentTransaction ? $invoice->paymentTransaction->payment_status : 2;
        array_push($invoice_arr, $temp);

        return $this->sendResponseWithData($invoice_arr, "invoice retrieved successfully.");
    }

    public function cancel_invoice(Request $request)
    {
        $rules = [
            'invoice_id' => 'required|integer|exists:invoice,invoice_id,deleted_at,NULL',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $invoice = Invoice::find($request->invoice_id);
            if (!$invoice) {
                return $this->sendError(400,'Invoice Not Found.', "Not Found", []);
            }
            $paymentTransaction = PaymentTransaction::where('invoice_id', $invoice->invoice_id)->first();

            if ($paymentTransaction && $paymentTransaction->payment_status == 1) {
                $paymentTransaction->delete();
            }
            $invoice->estatus = 3;
            $invoice->save();
            DB::commit();
            return $this->sendResponseSuccess("Invoice canceled successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while canceling the invoice.', "Internal Server Error", []);
        }
    }

    public function pay_invoice_payment(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'invoice_id' => 'required|exists:invoice,invoice_id,deleted_at,NULL',
            'payment_mode' => 'required|integer|in:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {

            $invoice = Invoice::find($request->invoice_id);
            if (!$invoice) {
                return $this->sendError(400,'Invoice Not Found.', "Not Found", []);
            }

            $paymentTransaction = new PaymentTransaction();
            $paymentTransaction->invoice_id = $request->invoice_id;
            $paymentTransaction->transaction_no = generateTransactionNumber();
            $paymentTransaction->payment_currency = "INR";
            $paymentTransaction->transaction_amount = $invoice->invoice_amount;
            $paymentTransaction->payment_mode = $request->payment_mode;
            $paymentTransaction->payment_status = 1;
            $paymentTransaction->created_by = Auth::user()->user_id;
            $paymentTransaction->updated_by = Auth::user()->user_id;
            $paymentTransaction->save();

            $ledger = SocietyLedgerDetail::where('society_id',$society_id)->first();
            $ledger->current_balance = $ledger->current_balance  + $invoice->invoice_amount;
            $ledger->total_balance = $ledger->total_balance + $invoice->invoice_amount;
            $ledger->save();

            DB::commit();
            $data = array();
            $temp['payment_transaction_id'] = $paymentTransaction->payment_transaction_id;
            array_push($data, $temp);
            return $this->sendResponseWithData($data, "Payment Successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(500, 'An error occurred while updating the payment.', "Internal Server Error", []);
        }
    }
}
