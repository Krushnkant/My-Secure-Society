<?php

use App\Models\CompanyDesignationAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;



function getUserDesignationId(){
    $user = Auth::user();
    if ($user->userdesignation) {
        return $user->userdesignation->company_designation_id;
    }
    return null;
}

function is_view($module_id){
    $user_designation_id = getUserDesignationId();
    $is_view = CompanyDesignationAuthority::where('company_designation_id',$user_designation_id)->where('eAuthority',$module_id)->where('can_view',1)->first();
    if ($is_view){
        return 1;
    }
    return 0;
}

function is_add($module_id){
    $user_designation_id = getUserDesignationId();
    $is_add = CompanyDesignationAuthority::where('company_designation_id',$user_designation_id)->where('eAuthority',$module_id)->where('can_add',1)->first();
    if ($is_add){
        return 1;
    }
    return 0;
}

function is_edit($module_id){
    $user_designation_id = getUserDesignationId();
    $is_edit = CompanyDesignationAuthority::where('company_designation_id',$user_designation_id)->where('eAuthority',$module_id)->where('can_edit',1)->first();
    if ($is_edit){
        return 1;
    }
    return 0;
}

function is_delete($module_id){
    $user_designation_id = getUserDesignationId();
    $is_delete = CompanyDesignationAuthority::where('company_designation_id',$user_designation_id)->where('eAuthority',$module_id)->where('can_delete',1)->first();
    if ($is_delete){
        return 1;
    }
    return 0;
}

function is_print($module_id){
    $user_designation_id = getUserDesignationId();
    $is_print = CompanyDesignationAuthority::where('company_designation_id',$user_designation_id)->where('eAuthority',$module_id)->where('can_print',1)->first();
    if ($is_print){
        return 1;
    }
    return 0;
}



function UploadImage($image, $path){
    $imageName = Str::random().'.'.$image->getClientOriginalExtension();
    $path = $image->move(public_path($path), $imageName);
    if($path == true){
        return $imageName;
    }else{
        return null;
    }
}

function getModulesArray()
{
    return [
        1 => 'Designation',
        2 => 'Designation Authority',
        3 => 'User & User Designation',
        4 => 'Government Emergency No',
        5 => 'Business Category',
        6 => 'Post Status Banner',
        7 => 'Society',
        8 => 'Society Block',
        9 => 'Block Flat',
        10 => 'Subscription Order',
        11 => 'Order Payment',
        12 => 'Company Profile',
        13 => 'Service Vendor',
        14 => 'Daily Help Service',
    ];
}

function getUserType($user_type_id){
    if($user_type_id == 1){
        $user_type = "Company Admin User";
    }
    elseif($user_type_id == 2){
        $user_type = "Resident App User";
    }
    elseif($user_type_id == 3){
        $user_type = "Guard App User";
    }
    elseif($user_type_id == 4){
        $user_type = "App User";
    }
    elseif($user_type_id == 5){
        $user_type = "Daily Help User";
    }
    elseif($user_type_id == 6){
        $user_type = "Staff Member";
    }else{
        $user_type = "";
    }
    return  $user_type;
}
