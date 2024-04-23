<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenity;
use App\Models\AmenitySlot;

class AmenityController extends BaseController
{
    public function save_amenity(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'is_chargable' => 'required|in:1,2',
            'allowed_payment_type' => 'required|in:1,2,3',
            'max_booking_per_slot' => 'required|integer',
            'max_people_per_booking' => 'required|integer',
            'image_files' => 'required|array|min:1|max:5',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:2048',
            'slot_list' => 'required|array|min:1',
            'slot_list.*.from_time' => 'required|date_format:H:i',
            'slot_list.*.to_time' => 'required|date_format:H:i|after:slot_list.*.from_time',
            'slot_list.*.booking_fee' => 'required|numeric',
            'slot_list.*.is_deleted' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Handle the request data and save the amenity details
        $amenity = new Amenity();
        $amenity->title = $request->title;
        $amenity->description = $request->description;
        $amenity->is_chargable = $request->is_chargable;
        $amenity->allowed_payment_type = $request->allowed_payment_type;
        $amenity->max_booking_per_slot = $request->max_booking_per_slot;
        $amenity->max_people_per_booking = $request->max_people_per_booking;
        $amenity->save();

        // Handle file uploads for images
        foreach ($request->file('image_files') as $image) {
            // Process and save each image file
        }

        // Handle file upload for PDF
        if ($request->hasFile('pdf_file')) {
            // Process and save the PDF file
        }

        // Save slot data
        foreach ($request->slot_list as $slotData) {
            $slot = new AmenitySlot();
            $slot->amenity_id = $amenity->id;
            $slot->from_time = $slotData['from_time'];
            $slot->to_time = $slotData['to_time'];
            $slot->booking_fee = $slotData['booking_fee'];
            $slot->is_deleted = $slotData['is_deleted'];
            $slot->save();
        }

        return response()->json(['message' => 'Amenity saved successfully'], 200);
    }
}
