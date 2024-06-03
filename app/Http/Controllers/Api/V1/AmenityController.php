<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenity;
use App\Models\AmenityFile;
use App\Models\AmenitySlot;
use App\Models\AmenityBooking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;

class AmenityController extends BaseController
{

    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function save_amenity(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }
        $request->merge(['society_id'=>$society_id]);

        // Custom validation rule for slot time conflict
        Validator::extend('slot_conflict', function ($attribute, $value, $parameters, $validator) use ($request) {
            $slots = $request->slot_list;
            foreach ($slots as $index => $slot) {
                foreach ($slots as $subIndex => $subSlot) {
                    if ($index != $subIndex) {
                        if (
                            ($slot['from_time'] >= $subSlot['from_time'] && $slot['from_time'] < $subSlot['to_time']) ||
                            ($slot['to_time'] > $subSlot['from_time'] && $slot['to_time'] <= $subSlot['to_time'])
                        ) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }, 'Slot times conflict with each other.');

        $rules = [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'is_chargable' => 'required|in:1,2',
            'booking_type' => 'required|in:1,2',
            'max_capacity' => 'required|integer',
            'image_files' => 'required|array|min:1|max:5',
            'image_files.*.file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_files.*.amenity_file_id' => 'nullable|integer',
            'image_files.*.is_deleted' => 'required|in:1,2',
            'slot_list' => 'required|array|min:1|slot_conflict',
            'slot_list.*.from_time' => 'required|date_format:H:i',
            'slot_list.*.to_time' => 'required|date_format:H:i|after:slot_list.*.from_time',
            'slot_list.*.booking_fee' => 'required|numeric',
            'slot_list.*.is_deleted' => 'required|in:1,2',
        ];

        if ($request->is_chargable == 1) {
            $rules['allowed_payment_type'] = 'required|in:1,2,3';
        }


        $messages = [
            'image_files.*.is_deleted.required' => 'image is_delete field is required.',
            'slot_list.*.is_deleted.required' => 'slot list is_delete field is required.',
            'slot_list.*.to_time.date_format' => 'Send Time format in H:i',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        if ($request->has('amenity_id') && $request->amenity_id != 0) {
            $amenity = Amenity::find($request->amenity_id);
            if (!$amenity) {
                return $this->sendError(404, 'amenity not found.', "Not Found", []);
            }
            $amenity->updated_by = Auth::user()->user_id;
        } else {
            $amenity = new Amenity();
            $amenity->created_by = Auth::user()->user_id;
            $amenity->updated_by = Auth::user()->user_id;
        }

        $amenity->society_id = $request->society_id;
        $amenity->amenity_name = $request->title;
        $amenity->amenity_description = $request->description;
        $amenity->is_chargeable = $request->is_chargable;
        if ($request->is_chargable == 1) {
            $amenity->applicable_payment_type = 3;
        }
        $amenity->booking_type = $request->booking_type;
        $amenity->max_capacity = $request->max_capacity;
        $amenity->save();


        if ($request->has('image_files')) {
            foreach ($request->image_files as $imageFile) {
                if ($imageFile['is_deleted'] == 2) {
                    if (isset($imageFile['file'])) {
                        $file = $imageFile['file'];
                        $fileType = getFileType($file);
                        $fileUrl = UploadImage($file, 'images/amenity');
                        $this->storeFileEntry($amenity->amenity_id, $fileType, $fileUrl);
                    }
                } elseif ($imageFile['is_deleted'] == 1 && isset($imageFile['amenity_file_id'])) {
                    // Handle deleting existing files if needed
                    AmenityFile::destroy($imageFile['amenity_file_id']);
                }
            }
        }

        // Handle file upload for PDF
        // if ($request->hasFile('pdf_file')) {
        //     $file = $request->file('pdf_file');
        //     $fileType = getFileType($file);
        //     $fileUrl = UploadImage($file,'images/amenity');
        //     $this->storeFileEntry($amenity->amenity_id, $fileType, $fileUrl);
        // }

        // Save slot data
        foreach ($request->slot_list as $slotData) {
            if ($slotData['is_deleted'] == 2) {
                $slot = new AmenitySlot();
                $slot->amenity_id = $amenity->amenity_id;
                $slot->entry_time = $slotData['from_time'];
                $slot->exit_time = $slotData['to_time'];
                $slot->rent_amount = $slotData['booking_fee'];
                $slot->created_by = Auth::user()->user_id;
                $slot->updated_by = Auth::user()->user_id;
                $slot->save();
            } elseif ($slotData['is_deleted'] == 1 && isset($slotData['amenity_slot_id'])) {
                // Handle deleting existing slots if needed
                AmenitySlot::destroy($slotData['amenity_slot_id']);
            }
        }

        if ($request->has('amenity_id') && $request->amenity_id == 0) {
            $notification_array['title'] = $amenity->title;
            $notification_array['message'] = $amenity->description;
            sendPushNotification($amenity->creategd_by,$notification_array,'amenity');
        }
        $data = array();
        $temp['amenity_id'] = $amenity->amenity_id;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Amenity saved successfully.");
    }

    public function storeFileEntry($Id, $fileType, $fileUrl)
    {
        $fileEntry = new AmenityFile();
        $fileEntry->amenity_id = $Id;
        $fileEntry->file_type = $fileType;
        $fileEntry->file_url = $fileUrl;
        $fileEntry->uploaded_at = now();
        $fileEntry->save();
    }

    public function amenity_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }
        $search_text = $request->input('search_text');
        $AmenitiesQuery = Amenity::with('amenity_images')
                                ->where('society_id', $society_id)
                                ->where('estatus', 1);

        if ($search_text) {
            $AmenitiesQuery->where(function ($query) use ($search_text) {
                $query->where('amenity_name', 'like', '%' . $search_text . '%')
                    ->orWhere('amenity_description', 'like', '%' . $search_text . '%');
            });
        }

        $amenities = $AmenitiesQuery->orderBy('created_at', 'DESC')->paginate(10);

        $amenity_arr = [];
        foreach ($amenities as $amenity) {
            $image_urls = [];
            foreach ($amenity->amenity_images as $image) {
                $image_urls[] = url($image->file_url);
            }

            $temp['amenity_id'] = $amenity->amenity_id;
            $temp['title'] = $amenity->amenity_name;
            $temp['description'] = $amenity->amenity_description;
            $temp['is_chargable'] = $amenity->is_chargeable;
            $temp['allowed_payment_type'] = $amenity->applicable_payment_type;
            $temp['booking_type'] = $amenity->booking_type;
            $temp['max_capacity'] = $amenity->max_capacity;
            $temp['image_urls'] = $image_urls;

            array_push($amenity_arr, $temp);
        }

        $data['amenity_list'] = $amenity_arr;
        $data['total_records'] = $amenities->toArray()['total'];
        return $this->sendResponseWithData($data, "All Amenity Successfully.");
    }

    public function amenity_slot_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL,society_id,' . $society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422, $validator->errors(), "Validation Errors", []);
        }

        $amenity = Amenity::where('estatus', 1)->where('amenity_id', $request->amenity_id)->first();

        if (!$amenity) {
            return $this->sendError(404, "You cannot get this amenity", "Invalid amenity", []);
        }

        $max_capacity = $amenity->max_capacity;
        $slots = AmenitySlot::where('amenity_id', $request->amenity_id)->where('estatus', 1)->get();

        $slot_arr = [];
        foreach ($slots as $slot) {
            $booking_count = AmenityBooking::where('amenity_slot_id', $slot->amenity_slot_id)
                ->where('booking_status', 1) // Assuming booking_status 1 means confirmed bookings
                ->count();

            $available_booking = $max_capacity - $booking_count;

            $temp['slot_id'] = $slot->amenity_slot_id;
            $temp['from_time'] = $slot->entry_time;
            $temp['to_time'] = $slot->exit_time;
            $temp['booking_fee'] = $slot->rent_amount;
            $temp['available_booking'] = $available_booking;
            array_push($slot_arr, $temp);
        }

        $data['slot_list'] = $slot_arr;
        return $this->sendResponseWithData($data, "All Amenity Slots Successfully.");
    }

    public function delete_amenity(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL,society_id,'.$society_id,
        ]);
        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $amenity = Amenity::find($request->amenity_id);
        if (!$amenity) {
            return $this->sendError(404, 'Amenity not found.', "Not Found", []);
        }

         // Check for future bookings
        $futureBookings = AmenityBooking::where('amenity_id', $request->amenity_id)
        ->where('end_date', '>', now())
        ->exists();

        if ($futureBookings) {
            return $this->sendError(400, 'The Amenity cannot be deleted due to this Amenity has been booked for the future days.', "Deletion Error", []);
        }

        // Soft delete the amenity
        $amenity->estatus = 3;
        $amenity->save();
        $amenity->delete();

        return $this->sendResponseSuccess("Amenity deleted Successfully.");

    }

    public function get_amenity(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL,society_id,'.$society_id,
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        $amenity = Amenity::with('amenity_images','amenity_slots')->where('estatus',1)->where('amenity_id',$request->amenity_id)->first();

        if (!$amenity){
            return $this->sendError(404,"You can not get this amenity", "Invalid amenity", []);
        }

        $image_urls = [];
        foreach ($amenity->amenity_images as $image) {
            $image_urls[] = url($image->file_url);
        }

        $slot_arr = [];
        foreach($amenity->amenity_slots as $slot){
            $booking_count = AmenityBooking::where('amenity_slot_id', $slot->amenity_slot_id)
            ->where('booking_status', 1) // Assuming booking_status 1 means confirmed bookings
            ->count();

            $available_booking = $amenity->max_capacity - $booking_count;

            $slot_temp['slot_id'] = $slot->amenity_slot_id;
            $slot_temp['from_time'] = $slot->entry_time;
            $slot_temp['to_time'] = $slot->exit_time;
            $slot_temp['booking_fee'] = $slot->rent_amount;
            $slot_temp['available_booking'] = $available_booking;
            array_push($slot_arr, $slot_temp);
        }
        $data = array();
        $temp['amenity_id'] = $amenity->amenity_id;
        $temp['title'] = $amenity->amenity_name;
        $temp['description'] = $amenity->amenity_description;
        $temp['is_chargable'] = $amenity->is_chargeable;
        $temp['allowed_payment_type'] = $amenity->applicable_payment_type;
        $temp['booking_type'] = $amenity->booking_type;
        $temp['max_capacity'] = $amenity->max_capacity;
        $temp['image_urls'] = $image_urls;
        $temp['slot_list'] = $slot_arr;
        array_push($data, $temp);
        return $this->sendResponseWithData($data, "Get Amenity Details Successfully.");
    }

    public function create_amenity_booking(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $block_flat_id = $this->payload['block_flat_id'];
        if($block_flat_id == ""){
            return $this->sendError(400,'Block flat Not Found.', "Not Found", []);
        }

        $validator = Validator::make($request->all(), [
            'amenity_id' => 'required|exists:amenity,amenity_id,deleted_at,NULL,society_id,'.$society_id,
            'slot_id' => 'required|exists:amenity_slot,amenity_slot_id,deleted_at,NULL,amenity_id,'.$request->amenity_id,
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_amount' => 'required|numeric|min:0',
            'gateway_name' => 'required|max:100', // Gateway name is required
            'payment_mode' => 'required|in:1,2,3', // Payment mode must be required and can only be 1, 2, or 3
            'payment_status' => 'required|in:1,2,3,4,5,6', // Payment status must be required and can only be 1, 2, 3, 4, 5, or 6
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \DB::beginTransaction();
        try {

        // Handle the request data and save the amenity details
        $amenity = new AmenityBooking();
        $amenity->user_id = Auth::id();
        $amenity->amenity_id = $request->amenity_id;
        $amenity->booking_no = generateBookingNumber();
        $amenity->amenity_slot_id = $request->slot_id;
        $amenity->start_date = $request->start_date;
        $amenity->end_date = $request->end_date;
        $amenity->total_amount = $request->total_amount;
        $amenity->booking_status = 2;
        $amenity->created_by = Auth::user()->user_id;
        $amenity->updated_by = Auth::user()->user_id;
        $amenity->save();


        if ($amenity) {
            // Create invoice
            $invoice = new Invoice();
            $invoice->invoice_type = 6; // Amenity Booking
            $invoice->invoice_to_user_type = 1; // Assuming it's a society member
            $invoice->block_flat_id = $block_flat_id;
            $invoice->invoice_to_user_id = Auth::user()->user_id;
            $invoice->invoice_user_name = Auth::user()->full_name;
            $invoice->invoice_no = generateInvoiceNumber($society_id);// You may have a function to generate invoice numbers
            $invoice->invoice_period_start_date = $request->start_date;
            $invoice->invoice_period_end_date = $request->end_date;
            $invoice->due_date = $request->start_date; // You may have a function to calculate due date
            $invoice->invoice_amount = $request->total_amount;
            $invoice->created_by = Auth::user()->user_id;
            $invoice->updated_by = Auth::user()->user_id;
            $invoice->save();

            // Create invoice item
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $invoice->invoice_id;
            $invoiceItem->invoice_item_type = 6; // Amenity Booking
            $invoiceItem->invoice_item_master_id = $amenity->amenity_booking_id;
            $invoiceItem->invoice_item_description = "Amenity Booking";
            $invoiceItem->invoice_item_amount = $request->total_amount;
            $invoiceItem->updated_by = Auth::user()->user_id;
            $invoiceItem->updated_at = now();
            $invoiceItem->save();

            // Create payment transaction
            $paymentTransaction = new PaymentTransaction();
            $paymentTransaction->invoice_id = $invoice->invoice_id;
            $paymentTransaction->transaction_no = generateTransactionNumber();
            $paymentTransaction->payment_currency = "INR";
            $paymentTransaction->transaction_amount = $request->total_amount;
            $paymentTransaction->gateway_name = $request->gateway_name;
            $paymentTransaction->payment_mode = $request->payment_mode;
            $paymentTransaction->payment_status = $request->payment_status;
            $paymentTransaction->created_by = Auth::user()->user_id;
            $paymentTransaction->updated_by = Auth::user()->user_id;
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

    public function amenity_booking_list(Request $request)
    {
        $society_id = $this->payload['society_id'];
        if ($society_id == "") {
            return $this->sendError(400, 'Society Not Found.', "Not Found", []);
        }

        $rules = [
            'amenity_id' => 'required',
            'user_id' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        if ($request->input('user_id') != 0) {
            $rules['user_id'] .= '|exists:user,user_id,deleted_at,NULL';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }


        $startDate = $request->input('start_date', now()->subMonth()); // Default to one month ago if start_date is not provided
        $endDate = $request->input('end_date', now()); // Default to current date if end_date is not provided


        $amenities_booking = AmenityBooking::with(['amenity.amenity_images', 'slot'])
                            ->whereHas('amenity', function($query) use ($society_id) {
                                $query->where('society_id', $society_id);
                            });

        $designation_id = $this->payload['designation_id'];
        if(getResidentDesignation($designation_id) == "Society Member"){
            $amenities_booking->where('user_id', Auth::user()->user_id);
        }else{
            if ($request->input('user_id') != 0) {
               $amenities_booking->where('user_id', $request->user_id);
            }
        }

        if ($request->amenity_id != 0) {
            $amenity = Amenity::find($request->amenity_id);
            if (!$amenity) {
                return $this->sendError(404, 'Amenity not found.', "Not Found", []);
            }
            $amenities_booking->where('amenity_id', $request->amenity_id);
        }

        if ($request->filled('start_date')) {
            $amenities_booking->whereDate('start_date', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $amenities_booking->whereDate('end_date', '<=', $endDate);
        }
        $amenities_booking = $amenities_booking->orderBy('created_at', 'DESC')->paginate(10);

        $amenity_booking_arr = [];
        foreach ($amenities_booking as $booking) {

            $image_files = [];
            if(isset($booking->amenity->amenity_images)){
                foreach ($booking->amenity->amenity_images as $image_file) {
                    $file_temp['file_id'] = $image_file->amenity_file_id;
                    $file_temp['file_url'] = url($image_file->file_url);
                    array_push($image_files, $file_temp);
                }
            }

            $temp['booking_id'] = $booking->amenity_booking_id;
            $temp['user_id'] = $booking->user_id;
            $temp['amenity_id'] = $booking->amenity_id;
            $temp['amenity_name'] = $booking->amenity->amenity_name;
            $temp['amenity_description'] = $booking->amenity->amenity_description;
            $temp['image_files'] = $image_files;
            $temp['start_date'] = $booking->start_date;
            $temp['end_date'] = $booking->end_date;
            $temp['entry_time'] = $booking->slot->entry_time;
            $temp['exit_time'] = $booking->slot->exit_time;
            $temp['total_amount'] = $booking->total_amount;
            $temp['booking_status'] = $booking->booking_status;
            $temp['payment_status'] = $booking->payment_status;
            array_push($amenity_booking_arr, $temp);
        }

        $data['booking_list'] = $amenity_booking_arr;
        $data['total_records'] = $amenities_booking->toArray()['total'];
        return $this->sendResponseWithData($data, "All Amenity Booking Successfully.");
    }

    public function amenity_booking_change_status(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:amenity_booking,amenity_booking_id',
            'booking_status' => 'required|in:1,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Retrieve the booking
        $booking = AmenityBooking::find($request->input('booking_id'));

        if (!$booking) {
            return $this->sendError(404, 'Booking not found.', "Not Found", []);
        }

        // Check if the booking status is already canceled
        if ($booking->booking_status == 3) {
            return $this->sendError(400, 'Booking status cannot be updated as it is already canceled.', "Invalid", []);
        }


        $newStatus = $request->input('booking_status');

        $designation_id = $this->payload['society_id'];
        if(getResidentDesignation($designation_id) == "Society Member" && $newStatus == 1){
            return $this->sendError(401, 'You are not authorized', "Unauthorized", []);
        }

        if ($newStatus == 1) {
            $booking->booking_status = 1;
        } elseif ($newStatus == 3) {
            if (now()->greaterThan($booking->start_date)) {
                return $this->sendError(400, 'Cannot cancel booking after the start date.', "Invalid", []);
            }
            // $entryTime = strtotime($booking->created_at);
            // $currentTime = time();
            // Check if cancellation is allowed before 3 hours of entry time
          //  if (($entryTime - $currentTime) > (3 * 3600)) {
                if ($booking->payment_status == 1) {
                    $booking->payment_status = 5; // Refund payment status
                }
                $booking->booking_status = 3;

                // Invoice::where('invoice_type',6)->
            // } else {
            //     return $this->sendError(400,'Cannot cancel booking less than 3 hours before entry time.', "Invalid", []);
            // }
        }

        // Save the updated booking status
        $booking->save();
        return $this->sendResponseSuccess("Booking status updated successfully.");

    }

}
