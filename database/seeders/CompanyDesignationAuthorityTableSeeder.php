<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyDesignationAuthorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /* Designation Authority eNum */ 
    // 1 - Company Designation (View, Add, Edit, Delete, Print)
    // 2 - Company Designation Authority (View, Edit, Print)
    // 3 - Company User & User Designation (View, Add, Edit, Delete, Print)
    // 4 - Government Emergency No (View, Add, Edit, Delete, Print) 
    // 5 - Business Category (View, Add, Edit, Delete, Print)
    // 6 - Post Status Banner (View, Add, Edit, Delete, Print)
    // 7 - Society (View, Add, Edit, Delete, Print)
    // 8 - Society Block (View, Add, Edit, Delete, Print)
    // 9 - Block Flat (View, Add, Edit, Delete, Print)
    // 10 - Subscription Order (View, Add, Edit)
    // 11 - Order Payment (View, Edit, Delete, Print)
    // 12 - Company Profile (View, Edit, Print)
    // 13 - Service Vendor (View, Add, Edit, Delete, Print)
    // 14 - Daily Help Service (View, Add, Edit, Delete, Print)
    // 15 - Post Status Banner Configuration (View, Print)
    // 16 - Push Notification (View, Add, Edit, Delete, Print)
    // 17 - Society Member (View, Print)
    // 18 - Business Profile & Business Profile Category (View, Print)


    ////////////////////////////////////////////
        // 13 - Society Member Designation (View, Print)
        // 15 - Society Charge (View, Print)
        // 16 - Society Guard Standing Area (View, Print)
        // 17 - Society Guard (View, Print)
        // 18 - Society Guard Visit Area (View, Print)
        // 19 - Guard Campus Visit Time (View, Print)
        // 20 - Guard Campus Visit Attendance (View, Print)
        // 21 - User Device Info (View, Print)
        // 22 - Invoice, Invoice Item & Payment Transaction (View, Print)
        // 24 - Society Ledger Detail (View, Print)
        // 25 - Loan Request (View, Print)
        // 26 - Term And Condition (View, Print)
        // 27 - Announcement (View, Print)
        // 30 - Daily Help Provider (View, Print)
        // 31 - Daily Help Provider Review (View, Print)
        // 32 - Visitor Gate Pass & Society Visit Code (View, Print)
        // 33 - Society Visitor (View, Print)
        // 34 - Delivered Courier At Gate (View, Print)
        // 35 - Society Document (View, Print)
        // 36 - User Emergency Contact (View, Print)
        // 37 - Amenity & AmenitySlot (View, Print)
        // 38 - Amenity Booking (View, Print)
        // 39 - Society Department (View, Print)
        // 40 - Service Category (View, Print)
        // 41 - Service Request (View, Print)
        // 42 - Society Staff Member (View, Print)
        // 44 - Society Panel Configuration (View, Print)
        // 45 - Society Forum Topic & Society Forum Topic Reply (View, Print)
        // 46 - Blood Donate Request & Blood Donate Request Response (View, Print)
        // 47 - Improvement Suggestion (View, Print)

    public function run(): void
    {
        DB::table('CompanyDesignationAuthority')->truncate();
        //canView, canAdd, canEdit, canDelete, canPrint (0 - Disabled, 1 - True, 2 - False)
        $DesignationAuthority = array(
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 1, // Company Designation
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 2, // Company Designation Authority
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 1, 
                "canDelete" => 0, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 3, // Company User & User Designation 
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 4, // Government Emergency No
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 5, // Business Category
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 6, // Post Status Banner
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 7, // Society
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 8, // Society Block
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 9, // Block Flat
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 10, // Subscription Order
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 11, // Order Payment
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 12, // Company Profile
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 1, 
                "canDelete" => 0, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 13, // Service Vendor
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 14, // Daily Help Service
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 15, // Post Status Banner
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 0, 
                "canDelete" => 0, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 16, // Push Notification
                "canView" => 1, 
                "canAdd" => 1, 
                "canEdit" => 1, 
                "canDelete" => 1, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 17, // Society Member
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 0, 
                "canDelete" => 0, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            ),
            array( 
                'CompanyDesignationId' => 1, 
                "eAuthority" => 18, // Business Profile & Business Profile Category
                "canView" => 1, 
                "canAdd" => 0, 
                "canEdit" => 0, 
                "canDelete" => 0, 
                "canPrint" => 1, 
                "eStatus" => 1,
                "UpdatedAt" => Carbon::now(), 
                "UpdatedBy" => 1
            )
        );
        DB::table('CompanyDesignationAuthority')->insert($DesignationAuthority);
    }
}
