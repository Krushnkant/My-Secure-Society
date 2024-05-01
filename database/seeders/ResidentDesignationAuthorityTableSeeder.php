<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResidentDesignationAuthorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('resident_designate_auth')->truncate();
        //can_view, can_add, can_edit, can_delete, can_print (0 - Disabled, 1 - True, 2 - False)
        $DesignationAuthority = array(
            
            // Society Admin Authority
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 51, // Society Department
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 52, // Category for Society
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 53, // Society Member Designation
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 54, // Society Member Designation Authority
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 55, // Society Member List
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 56, // Society Member Request
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 57, // Announcement
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 58, // Amenity
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 59, // Amenity Booking
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 60, // Emergency Alert History
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 61, // Society Emergency No
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 62, // Resident's Society Payment
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 63, // Invoice
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 64, // Resident's Loan Request
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 65, // Resident's Complaint
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 66, // Duty Area
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 67, // Staff Member
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 68, // Staff Member Duty Area
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 69, // Staff Member Attendance
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 70, // Maintanance Terms
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 71, // Loan Terms
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 72, // Pre Approved List
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 73, // Visitor List
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 74, // Delivered At Gate
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 1, 
                "eAuthority" => 75, // Daily Help Member
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),

            // Committee Member Authority
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 51, // Society Department
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 52, // Category for Society
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 53, // Society Member Designation
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 54, // Society Member Designation Authority
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 2, 
                "can_delete" => 0, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 55, // Society Member List
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 56, // Society Member Request
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 57, // Announcement
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 58, // Amenity
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 59, // Amenity Booking
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 2, 
                "can_delete" => 0, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 60, // Emergency Alert History
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 61, // Society Emergency No
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 62, // Resident's Society Payment
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 63, // Invoice
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 64, // Resident's Loan Request
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 65, // Resident's Complaint
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 66, // Duty Area
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 67, // Staff Member
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 68, // Staff Member Duty Area
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 69, // Staff Member Attendance
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 70, // Maintanance Terms
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 71, // Loan Terms
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 72, // Pre Approved List
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 73, // Visitor List
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 74, // Delivered At Gate
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 2, 
                "eAuthority" => 75, // Daily Help Member
                "can_view" => 1, 
                "can_add" => 2, 
                "can_edit" => 2, 
                "can_delete" => 2, 
                "can_print" => 2, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),

            // Resident's Authority
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 1, // Own Flat
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 2, // Own Family Member
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1,  
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 3, // Own Festival Banner 
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 4, // Own Festival Banner Configuration
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 5, // Own Folder
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 6, // Own Documents
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 7, // Society Member List
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 8, // Announcement
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 9, // Resident's Daily Post
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 10, // Own Daily Post
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 11, // Amenity
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 12, // Amenity Booking
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 13, // Emergency Alert
                "can_view" => 0, 
                "can_add" => 1, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 0, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 14, // My Emergency No
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 15, // Soc Emergency No
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 16, // Government Emergency No
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 17, // Resident's Business Profile
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 18, // Own Business Profile
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 19, // Resident's Society Payment
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 20, // Invoice
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 21, // Own Loan Request
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 22, // Own Complaint 
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 0, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 23, // Staff Member
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 24, // Staff Member Duty Area
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 25, // Staff Member Attendance
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 26, // Maintanance Terms
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 27, // Loan Terms
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 28, // Pre Approved List
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 29, // Own Visitor List
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 0, 
                "can_delete" => 0, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 30, // Delivered At Gate
                "can_view" => 1, 
                "can_add" => 0, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 31, // Daily Help Member
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
            array( 
                'resident_designation_id' => 3, 
                "eAuthority" => 32, // Daily Help Member for My Flat
                "can_view" => 1, 
                "can_add" => 1, 
                "can_edit" => 1, 
                "can_delete" => 1, 
                "can_print" => 1, 
                "estatus" => 1,
                "updated_at" => Carbon::now(), 
                "updated_by" => 1
            ),
        );
        DB::table('resident_designate_auth')->insert($DesignationAuthority);
    }
}
   