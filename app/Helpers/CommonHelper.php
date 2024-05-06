<?php

use App\Models\CompanyDesignationAuthority;
use App\Models\Flat;
use App\Models\ResidentDesignationAuthority;
use App\Models\Society;
use App\Models\SocietyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

function getUserDesignationId()
{
    $user = Auth::user();
    if ($user->userdesignation) {
        return $user->userdesignation->company_designation_id;
    }
    return null;
}

function is_view($module_id)
{
    $user_designation_id = getUserDesignationId();
    $is_view = CompanyDesignationAuthority::where('company_designation_id', $user_designation_id)->where('eAuthority', $module_id)->where('can_view', 1)->first();
    if ($is_view) {
        return 1;
    }
    return 0;
}

function is_add($module_id)
{
    $user_designation_id = getUserDesignationId();
    $is_add = CompanyDesignationAuthority::where('company_designation_id', $user_designation_id)->where('eAuthority', $module_id)->where('can_add', 1)->first();
    if ($is_add) {
        return 1;
    }
    return 0;
}

function is_edit($module_id)
{
    $user_designation_id = getUserDesignationId();
    $is_edit = CompanyDesignationAuthority::where('company_designation_id', $user_designation_id)->where('eAuthority', $module_id)->where('can_edit', 1)->first();
    if ($is_edit) {
        return 1;
    }
    return 0;
}

function is_delete($module_id)
{
    $user_designation_id = getUserDesignationId();
    $is_delete = CompanyDesignationAuthority::where('company_designation_id', $user_designation_id)->where('eAuthority', $module_id)->where('can_delete', 1)->first();
    if ($is_delete) {
        return 1;
    }
    return 0;
}

function is_print($module_id)
{
    $user_designation_id = getUserDesignationId();
    $is_print = CompanyDesignationAuthority::where('company_designation_id', $user_designation_id)->where('eAuthority', $module_id)->where('can_print', 1)->first();
    if ($is_print) {
        return 1;
    }
    return 0;
}



function UploadImage($image, $path)
{
    $imageName = Str::random() . '.' . $image->getClientOriginalExtension();
    $tempName = $image->getPathname();
    $imageSize = $image->getSize();

    if (!file_exists(public_path($path))) {
        mkdir(public_path($path), 0755, true);
    }

    if ($image->isValid() && strpos($image->getMimeType(), 'image/') === 0) {
        $destination = public_path($path) . '/' . $imageName;

        if (file_exists($destination)) {
            $imageName = Str::random() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path($path) . '/' . $imageName;
        }

        if ($imageSize > 100000) { // 100 KB in bytes
            $file = compressImage($tempName, $destination, 30);
        } else {
            $file = $image->move(public_path($path), $imageName);
        }
        }else{
            $file = $image->move(public_path($path), $imageName);
        }

    if ($file) {
        return $path.'/'.$imageName;
    } else {
        return null;
    }
}

function compressImage($source, $destination, $quality) {
    $imgInfo = getimagesize($source);
    $mime = $imgInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($source);
            break;
        default:
            $image = @imagecreatefromjpeg($source);
    }

    // Save compressed image with quality parameter
    if ($mime === 'image/png') {
        imagejpeg($image, $destination, round(9 * $quality / 100));
    } else {
        imagejpeg($image, $destination, $quality);
    }

    // Destroy the image resource
    imagedestroy($image);

    // Return compressed image path
    return $destination;
}

function getFileType($file)
{
    $extension = strtolower($file->getClientOriginalExtension());
    $fileTypes = [
        'jpg' => 1, 'jpeg' => 1, 'png' => 1, 'gif' => 1,
        'pdf' => 4,
        'mp4' => 2, 'mov' => 2, 'avi' => 2, 'wmv' => 2, 'mkv' => 2
    ];
    if (array_key_exists($extension, $fileTypes)) {
        return $fileTypes[$extension];
    }
    return 5;
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

function getUserType($user_type_id)
{
    if ($user_type_id == 1) {
        $user_type = "Company Admin User";
    } elseif ($user_type_id == 2) {
        $user_type = "Resident App User";
    } elseif ($user_type_id == 3) {
        $user_type = "Guard App User";
    } elseif ($user_type_id == 4) {
        $user_type = "App User";
    } elseif ($user_type_id == 5) {
        $user_type = "Daily Help User";
    } elseif ($user_type_id == 6) {
        $user_type = "Staff Member";
    } else {
        $user_type = "";
    }
    return  $user_type;
}

function send_sms($mobile_no, $otp)
{
    $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=H26o0GZiiEaUyyy0kvOV5g&senderid=MADMRT&channel=2&DCS=0&flashsms=0&number=91' . $mobile_no . '&text=Welcome%20to%20Madness%20Mart,%20Your%20One%20time%20verification%20code%20is%20' . $otp . '.%20Regards%20-%20MADNESS%20MART&route=31&EntityId=1301164983812180724&dlttemplateid=1307165088121527950';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    //    echo $response;
}

function getSocietyBlockAndFlatInfo($flatId)
{
    $flatInfo = Flat::with('society_block.society')->findOrFail($flatId);
    $street_address = isset($flatInfo->society_block->society)?$flatInfo->society_block->society->street_address1:"";
    $society_name = isset($flatInfo->society_block->society)?$flatInfo->society_block->society->society_name:"";
    $block_name = isset($flatInfo->society_block)?$flatInfo->society_block->block_name:"";
    $block_id = isset($flatInfo->society_block)?$flatInfo->society_block->society_block_id:"";
    $flat_no = isset($flatInfo)?$flatInfo->flat_no:"";

    return compact('society_name', 'block_name', 'flat_no','street_address');
}


function getResidentDesignationId()
{
    $token = JWTAuth::parseToken()->getToken();
    $payload = JWTAuth::decode($token);
    $society_member_id = $payload['society_member_id'];
    $society_member = SocietyMember::where('society_member_id',$society_member_id)->where('estatus',1)->first();
    if ($society_member) {
        return $society_member->resident_designation_id;
    }
    return null;
}

function is_view_resident($module_id)
{
    $resident_designation_id = getResidentDesignationId();
    $is_view = ResidentDesignationAuthority::where('resident_designation_id', $resident_designation_id)->where('eAuthority', $module_id)->where('can_view', 1)->first();
    if ($is_view) {
        return 1;
    }
    return 0;
}

function is_add_resident($module_id)
{
    $resident_designation_id = getResidentDesignationId();
    $is_add = ResidentDesignationAuthority::where('resident_designation_id', $resident_designation_id)->where('eAuthority', $module_id)->where('can_add', 1)->first();
    if ($is_add) {
        return 1;
    }
    return 0;
}

function is_edit_resident($module_id)
{
    $resident_designation_id = getResidentDesignationId();
    $is_edit = ResidentDesignationAuthority::where('resident_designation_id', $resident_designation_id)->where('eAuthority', $module_id)->where('can_edit', 1)->first();
    if ($is_edit) {
        return 1;
    }
    return 0;
}

function is_delete_resident($module_id)
{
    $resident_designation_id = getResidentDesignationId();
    $is_delete = ResidentDesignationAuthority::where('resident_designation_id', $resident_designation_id)->where('eAuthority', $module_id)->where('can_delete', 1)->first();
    if ($is_delete) {
        return 1;
    }
    return 0;
}

function is_print_resident($module_id)
{
    $resident_designation_id = getResidentDesignationId();
    $is_print = ResidentDesignationAuthority::where('resident_designation_id', $resident_designation_id)->where('eAuthority', $module_id)->where('can_print', 1)->first();
    if ($is_print) {
        return 1;
    }
    return 0;
}


function isFlatInSociety($flatId, $societyId)
{
    // Retrieve the flat by ID and check if it belongs to the given society
    return Flat::where('block_flat_id', $flatId)
        ->whereHas('society_block', function ($query) use ($societyId) {
            $query->where('society_id', $societyId);
        })
        ->exists();
}

function generateTransactionNumber() {
    // You can generate a transaction number using various strategies, such as combining timestamp with random digits.
    return  time() . rand(1000, 9999);
}

/**
 * Generate a unique invoice number.
 *
 * @return string
 */
function generateInvoiceNumber($societyId) {
    // Get the current year
    $currentYear = date('Y');

    // Construct the invoice number format: YYYY+SocietyId+0001
    $invoiceNumber = $currentYear . '+' . $societyId . '+0001';

    // You may adjust the format or the logic to ensure uniqueness as needed

    return $invoiceNumber;
}

function calculateDueDate() {
    // Example: Due date is 30 days from the current date
    //return date('Y-m-d', strtotime('+30 days'));
    return date('Y-m-d');
}
