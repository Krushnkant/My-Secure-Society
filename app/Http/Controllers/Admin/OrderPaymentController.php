<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionOrder;
use App\Models\OrderPayment;

class OrderPaymentController extends Controller
{
    public function index($id) {
        $order = SubscriptionOrder::find($id);
        if($order == null){
            return view('admin.404');
        }
        return view('admin.order_payment.list',compact('id'));
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
        $query = OrderPayment::select('*')->where('subscription_order_id',$request->order_id);
        $search = $request->search;
        $query = $query->where(function($query) use ($search){
            $query->orWhere('order_payment_id', 'like', "%".$search."%");
        });

        $orderByName = 'order_payment_id';
        switch($orderColumnIndex){
            case '0':
                $orderByName = 'order_payment_id';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();

        return response()->json(["draw"=> $request->draw, "recordsTotal"=> $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request){
        $messages = [
            'amount_paid.required' =>'Please provide a paid amount',
            'payment_date.required' =>'Please provide a payment date',
        ];

        $validator = Validator::make($request->all(), [
            'amount_paid' => 'required',
            'payment_date' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $subscriptionOrder = SubscriptionOrder::find($request->subscription_order_id);
        if (!$subscriptionOrder) {
            return response()->json(['status' => '400']);
        }

        if ($subscriptionOrder->total_outstanding_amount < $request->amount_paid) {
            return response()->json(['status' => '300', 'message' => 'Total outstanding amount is less than amount paid']);
        }

        $paymentorder = new OrderPayment();
        $paymentorder->subscription_order_id = $request->subscription_order_id;
        $paymentorder->amount_paid = $request->amount_paid;
        $paymentorder->payment_date = $request->payment_date;
        $paymentorder->payment_type  = $request->payment_type;
        $paymentorder->payment_note  = $request->payment_note;
        $paymentorder->created_by = Auth::user()->user_id;
        $paymentorder->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        $paymentorder->save();


        $subscriptionOrder->total_paid_amount += $request->amount_paid;
        $subscriptionOrder->total_outstanding_amount = $subscriptionOrder->total_amount - $subscriptionOrder->total_paid_amount;
        $subscriptionOrder->save();

        return response()->json(['status' => '200', 'action' => 'add']);
    }



    public function delete($id){
        $paymentorder = OrderPayment::find($id);
        if ($paymentorder){
            $subscriptionOrder = SubscriptionOrder::find($paymentorder->subscription_order_id);
            if (!$subscriptionOrder) {
                return response()->json(['status' => '400']);
            }
            $subscriptionOrder->total_paid_amount -= $paymentorder->amount_paid;
            $subscriptionOrder->total_outstanding_amount += $paymentorder->amount_paid;
            $subscriptionOrder->save();
            $paymentorder->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }


    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $paymentorders = OrderPayment::whereIn('order_payment_id', $ids)->get();
        foreach ($paymentorders as $paymentorder) {
            $paymentorder->estatus = 3;
            $paymentorder->save();

            $subscriptionOrder = SubscriptionOrder::find($paymentorder->subscription_order_id);
            if ($subscriptionOrder) {
                $subscriptionOrder->total_paid_amount -= $paymentorder->amount_paid;
                $subscriptionOrder->total_outstanding_amount += $paymentorder->amount_paid;
                $subscriptionOrder->save();
            }
        }
        OrderPayment::whereIn('order_payment_id', $ids)->delete();

        return response()->json(['status' => '200']);
    }
}
