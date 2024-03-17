<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Society;
use App\Models\SubscriptionOrder;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SubscriptionOrderController extends Controller
{
    public function index()
    {
        $societies = Society::where('estatus',1)->get();
        return view('admin.subscription_order.list', compact('societies'));
    }

    public function listdata(Request $request)
    {

        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'ASC';

        // get data from products table
        $query = SubscriptionOrder::select('*')->with('society');
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('order_id', 'like', "%" . $search . "%");
        });

        $orderByName = 'order_id';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'order_id';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        return response()->json(["draw" => $request->draw, "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request)
    {
        $messages = [
            'society_id.required' => 'Please provide a society',
            'total_flat.required' => 'Please provide a total flat',
            'amount_per_flat.required' => 'Please provide a amount per flat',
            'sub_total_amount.required' => 'Please provide a sub total amount',
            'gst_percent.required' => 'Please provide a gst percent',
            'gst_amount.required' => 'Please provide a gst amount',
            'total_amount.required' => 'Please provide a total amount',
            'total_paid_amount.required' => 'Please provide a total paid amount',
            'total_outstanding_amount.required' => 'Please provide a total outstanding amount',
            'order_status.required' => 'Please provide a order status',
            'due_date.required' => 'Please provide a due date',
            'payment_date.required' => 'Please provide a payment date',
            'total_paid_amount.max' => 'Total paid amount cannot exceed total amount.',
        ];
        if (!isset($request->id)) {
            $validator = Validator::make($request->all(), [
                'society_id' => 'required',
                'total_flat' => 'required|numeric',
                'amount_per_flat' => 'required|numeric',
                'sub_total_amount' => 'required|numeric',
                'total_amount' => 'required|numeric',
                'gst_percent' => 'required|numeric',
                'gst_amount' => 'required|numeric',
                'total_paid_amount' => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($request) {
                        // Custom validation callback to check if total_paid_amount is not greater than total_amount
                        if ($value > $request->total_amount) {
                            $fail('Total paid amount cannot exceed total amount.');
                        }
                    },
                ],
                'total_outstanding_amount' => 'required|numeric',
                'order_status' => 'required',
                'due_date' => 'required',
                'payment_date' => 'required',
            ], $messages);
        }else{
            $validator = Validator::make($request->all(), [
                'order_status' => 'required',
                'due_date' => 'required',
            ], $messages);
        }
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }
        if (!isset($request->id)) {
            $subscriptionorder = new SubscriptionOrder();
            $subscriptionorder->created_by = Auth::user()->user_id;
            $subscriptionorder->society_id = $request->society_id;
            $subscriptionorder->order_id = 'ORD-' . Str::random(6);
            $subscriptionorder->total_flat = $request->total_flat;
            $subscriptionorder->amount_per_flat = $request->amount_per_flat;
            $subscriptionorder->sub_total_amount = $request->sub_total_amount;
            $subscriptionorder->gst_percent = $request->gst_percent;
            $subscriptionorder->gst_amount = $request->gst_amount;
            $subscriptionorder->total_amount = $request->total_amount;
            $subscriptionorder->total_paid_amount = $request->total_paid_amount;
            $subscriptionorder->total_outstanding_amount = $request->total_outstanding_amount;
            $subscriptionorder->order_status = $request->order_status;
            $subscriptionorder->due_date = $request->due_date;
            $subscriptionorder->updated_by = Auth::user()->user_id;
            $subscriptionorder->save();

            if($subscriptionorder){
                $order_payment = New OrderPayment();
                $order_payment->subscription_order_id = $subscriptionorder->subscription_order_id;
                $order_payment->payment_type = $request->payment_type;
                $order_payment->amount_paid = $request->total_paid_amount;
                $order_payment->payment_note = $request->payment_note;
                $order_payment->payment_date = $request->payment_date;
                $order_payment->created_by = Auth::user()->user_id;
                $order_payment->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                $order_payment->save();
            }
            return response()->json(['status' => '200', 'action' => 'add']);
        } else {
            $subscriptionorder = SubscriptionOrder::find($request->id);
            if (!$subscriptionorder) {
                return response()->json(['status' => '400']);
            }
            $subscriptionorder->order_status = $request->order_status;
            $subscriptionorder->due_date = $request->due_date;
            $subscriptionorder->updated_by = Auth::user()->user_id;
            $subscriptionorder->save();
            return response()->json(['status' => '200', 'action' => 'update']);
        }
    }
    public function edit($id)
    {
        $subscriptionorder = SubscriptionOrder::with('payment_order')->find($id);

        return response()->json($subscriptionorder);
    }
    public function delete($id)
    {
        $subscriptionorder = SubscriptionOrder::find($id);
        if ($subscriptionorder) {
            $subscriptionorder->estatus = 3;
            $subscriptionorder->save();
            $subscriptionorder->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id)
    {
        $subscriptionorder = SubscriptionOrder::find($id);
        if ($subscriptionorder->estatus == 1) {
            $subscriptionorder->estatus = 2;
            $subscriptionorder->save();
            return response()->json(['status' => '200', 'action' => 'deactive']);
        }
        if ($subscriptionorder->estatus == 2) {
            $subscriptionorder->estatus = 1;
            $subscriptionorder->save();
            return response()->json(['status' => '200', 'action' => 'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $subscriptionorders = SubscriptionOrder::whereIn('subscription_order_id', $ids)->get();
        foreach ($subscriptionorders as $subscriptionorder) {
            $subscriptionorder->estatus = 3;
            $subscriptionorder->save();
        }
        SubscriptionOrder::whereIn('subscription_order_id', $ids)->delete();
        return response()->json(['status' => '200']);
    }
}
