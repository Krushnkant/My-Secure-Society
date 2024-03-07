<?php

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Level;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

function getLeftMenuPages(){
    $pages = \App\Models\ProjectPage::where('parent_menu',0)->orderBy('sr_no','ASC')->get()->toArray();
    return $pages;
}

function getUserDesignation(){
    $user = Auth::user();

    // Check if the user has a designation
    if ($user->userdesignation) {
        return $user->userdesignation->company_designation_id;
    }

    return null;
}

function is_write($page_id){
    $is_write = \App\Models\UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_write',1)->first();
    if ($is_write){
        return true;
    }
    return false;
}

function is_delete($page_id){
    $is_delete = \App\Models\UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_delete',1)->first();
    if ($is_delete){
        return true;
    }
    return false;
}

function is_read($page_id){
    $is_read = \App\Models\UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_read',1)->first();
    if ($is_read){
        return true;
    }
    return false;
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
            1 => 'Company Designation',
            2 => 'Company Designation Authority',
            3 => 'Company User & User Designation',
            4 => 'Government Emergency No',
            5 => 'Business Category',
            6 => 'Post Status Banner',
            7 => 'Society',
            8 => 'Society Block',
            9 => 'Block Flat',
            10 => 'Subscription Order',
            11 => 'Order Payment',
            12 => 'Company Profile',
        ];
    }

