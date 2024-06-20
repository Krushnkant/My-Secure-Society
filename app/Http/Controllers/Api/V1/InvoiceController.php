<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
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
            'invoice_id' => 'required|integer',
            'block_flat_id' => 'required|array|exists:block_flat,block_flat_id,deleted_at,NULL',
            'invoice_type' => 'required|integer|in:1,2,3,4,5,6,7',
            'invoice_to_user_id' => 'required|integer',
            'invoice_amount' => 'required|numeric|min:0',
            'invoice_start_date' => 'nullable|date|after_or_equal:today',
            'invoice_end_date' => 'nullable|date|after_or_equal:invoice_start_date',
            'due_date' => 'nullable|date|after_or_equal:today',
            'gateway_name' => 'required|string|max:100',
            'payment_status' => 'required|integer|in:1,2',
            'payment_mode' => 'required_if:payment_status,1|integer|in:1,2',
            'list_of_items' => 'required|array',
            'list_of_items.*.charge_id' => 'required|integer',
            'list_of_items.*.description' => 'required|string|max:255',
            'list_of_items.*.charge_amount' => 'required|numeric|min:0',
            'list_of_items.*.is_deleted' => 'required|integer|in:1,2'
        ];

        if ($request->has('invoice_id') && $request->input('invoice_id') != 0) {
            $rules['invoice_id'] .= '|exists:invoice,invoice_id,deleted_at,NULL';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \DB::beginTransaction();
        try {

            $invoice = new Invoice();
            $invoice->invoice_type = $request->invoice_type;
            $invoice->invoice_to_user_type = $request->invoice_to_user_type;
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
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->charge_id = $item['charge_id'];
                $invoiceItem->description = $item['description'];
                $invoiceItem->charge_amount = $item['charge_amount'];
                $invoiceItem->is_deleted = $item['is_deleted'];
                $invoiceItem->updated_by = Auth::user()->id;
                $invoiceItem->save();
            }

            if ($request->payment_status == 1) {
                $paymentTransaction = new PaymentTransaction();
                $paymentTransaction->invoice_id = $invoice->id;
                $paymentTransaction->transaction_no = generateTransactionNumber();
                $paymentTransaction->transaction_amount = $request->invoice_amount;
                $paymentTransaction->gateway_name = $request->gateway_name;
                $paymentTransaction->payment_mode = $request->payment_mode;
                $paymentTransaction->payment_status = $request->payment_status;
                $paymentTransaction->created_by = Auth::user()->id;
                $paymentTransaction->updated_by = Auth::user()->id;
                $paymentTransaction->save();
            }


            \DB::commit();
            $data = array();
            $temp['amenity_booking_id'] = $amenity->amenity_booking_id;
            array_push($data, $temp);
            return $this->sendResponseWithData($data, "Amenity Booking Successfully.");
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->sendError(500, 'An error occurred while updating the post.', "Internal Server Error", []);
        }
    }
}
