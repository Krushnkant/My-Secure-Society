<?php

use App\Models\CompanyDesignationAuthority;
use App\Models\Flat;
use App\Models\ResidentDesignationAuthority;
use App\Models\ResidentDesignation;
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
        15 => 'Visiting Help Category',
    ];
}


function getAuthName($auth_id)
{
    // Mapping of authorization IDs to their respective names
    $authMap = [
        1 => "Own Flat",
        2 => "Own Family Member",
        3 => "Own Festival Banner",
        4 => "Own Festival Banner Configuration",
        5 => "Own Folder",
        6 => "Own Documents",
        7 => "Society Member List",
        8 => "Announcement",
        9 => "Resident's Daily Post",
        10 => "Own Daily Post",
        11 => "Amenity",
        12 => "Amenity Booking",
        13 => "Emergency Alert",
        14 => "My Emergency No",
        15 => "Soc Emergency No",
        16 => "Government Emergency No",
        17 => "Resident's Business Profile",
        18 => "Own Business Profile",
        19 => "Resident's Society Payment",
        20 => "Invoice",
        21 => "Own Loan Request",
        22 => "Own Complaint",
        23 => "Staff Member",
        24 => "Staff Member Duty Area",
        25 => "Staff Member Attendance",
        26 => "Maintenance Terms",
        27 => "Loan Terms",
        28 => "Pre Approved List / Gatepass",
        29 => "Own Visitor List",
        30 => "Delivered At Gate",
        31 => "Daily Help Member",
        32 => "Daily Help Member for My Flat",
        33 => "Society Department",
        34 => "Service Category",
        51 => "Society Department",
        52 => "Category for Society",
        53 => "Society Member Designation",
        54 => "Society Member Designation Authority",
        55 => "Society Member List",
        56 => "Society Member Request",
        57 => "Announcement",
        58 => "Amenity",
        59 => "Amenity Booking",
        60 => "Emergency Alert History",
        61 => "Society Emergency No",
        62 => "Resident's Society Payment",
        63 => "Invoice",
        64 => "Resident's Loan Request",
        65 => "Resident's Complaint",
        66 => "Duty Area",
        67 => "Staff Member",
        68 => "Staff Member Duty Area",
        69 => "Staff Member Attendance",
        70 => "Maintenance Terms",
        71 => "Loan Terms",
        72 => "Pre Approved List / Gatepass",
        73 => "Visitor List",
        74 => "Delivered At Gate",
        75 => "Daily Help Member",
        76 => "Service Category",
    ];

    // Return the corresponding authorization name
    return $authMap[$auth_id] ?? "Unknown Authorization";
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


function getSocietyBlockAndFlatInfo($flatId)
{
    $flatInfo = Flat::with('society_block.society')->findOrFail($flatId);
    $street_address = isset($flatInfo->society_block->society)?$flatInfo->society_block->society->street_address1:"";
    $society_name = isset($flatInfo->society_block->society)?$flatInfo->society_block->society->society_name:"";
    $block_name = isset($flatInfo->society_block)?$flatInfo->society_block->block_name:"";
    $block_id = isset($flatInfo->society_block)?$flatInfo->society_block->society_block_id:"";
    $flat_no = isset($flatInfo)?$flatInfo->flat_no:"";

    return compact('society_name', 'block_name', 'flat_no','street_address','block_id');
}

function getUserBlockAndFlat($userId)
{
    $flatInfo = SocietyMember::with('flat.society_block.society')->where('user_id',$userId)->first();
    return $flatInfo->flat->society_block->block_name .'-'.$flatInfo->flat->flat_no;
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

// Function to generate a new service request number
function generateServiceRequestNumber($societyId) {
    // Get the current year
    $currentYear = date('Y');

    // Find the latest service request number for the current year and society
    $latestRequest = \DB::table('service_request')
        ->where('society_id', $societyId)
        ->whereYear('created_at', $currentYear)
        ->orderBy('service_request_id', 'desc')
        ->first();

    if ($latestRequest) {
        // Extract the incrementing part from the latest service request number
        $latestNumber = explode('-', $latestRequest->service_request_number);
        $latestIncrement = (int) substr($latestNumber[2], -4);
        // Increment the number
        $newIncrement = $latestIncrement + 1;
    } else {
        // Start with 1 if no previous requests exist
        $newIncrement = 1;
    }

    // Format the incrementing part with leading zeros
    $formattedIncrement = str_pad($newIncrement, 4, '0', STR_PAD_LEFT);

    // Construct the new service request number: SR-YYYYSocietyId-0001
    $newServiceRequestNumber = 'SR-' . $currentYear . $societyId . '-' . $formattedIncrement;

    return $newServiceRequestNumber;
}

function generateLoanRequestNumber($societyId) {
    $currentYear = date('Y');

    $latestRequest = \DB::table('loan_request')
        ->where('society_id', $societyId)
        ->whereYear('created_at', $currentYear)
        ->orderBy('loan_request_id', 'desc')
        ->first();

    if ($latestRequest) {
        $latestNumber = explode('-', $latestRequest->loan_no);
        $latestIncrement = (int) substr($latestNumber[2], -4);
        $newIncrement = $latestIncrement + 1;
    } else {
        $newIncrement = 1;
    }

    $formattedIncrement = str_pad($newIncrement, 4, '0', STR_PAD_LEFT);
    $newServiceRequestNumber = 'L-' . $currentYear . $societyId . '-' . $formattedIncrement;
    return $newServiceRequestNumber;
}

function generateBookingNumber()
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($characters), 0, 8);
}

function calculateDueDate() {
    // Example: Due date is 30 days from the current date
    //return date('Y-m-d', strtotime('+30 days'));
    return date('Y-m-d');
}

function getResidentDesignation($designation_id)
{
    $residentDesignation = ResidentDesignation::where('resident_designation_id',$designation_id)->where('estatus',1)->first();
    return $residentDesignation ? $residentDesignation->designation_name : null;
}


function getReasonTypeName($reasonType)
{
    switch ($reasonType) {
        case 1:
            return 'Fire';
        case 2:
            return 'Stuck in Lift';
        case 3:
            return 'Animal Threat';
        default:
            return 'Other';
    }
}

function getStatusName($status)
{
    $statusNames = [
        1 => 'Closed',
        2 => 'Issue Raised',
        3 => 'In Progress',
        4 => 'ReOpened',
        5 => 'On Hold'
    ];
    return $statusNames[$status] ?? 'Unknown';
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

function sendPushNotification($user_id, $data,$type = null){
    // Do not send push notification from localhost
    if (env('APP_ENV') == 'local') {
        \Illuminate\Support\Facades\Log::info($data);
        \Illuminate\Support\Facades\Log::info("local environment");
        return true;
    }
    else{
        if($type == "amenity"){
            $tokens_android = \App\Models\User::where('user_id',"!=",$user_id)->where('user_type',4)->pluck('token')->all();
            $tokens_ios = \App\Models\User::where('user_id',"!=",$user_id)->where('user_type',4)->pluck('token')->all();
        }else{
            $tokens_android = \App\Models\User::where('user_id',"!=",$user_id)->where('user_type',4)->pluck('token')->all();
            $tokens_ios = \App\Models\User::where('user_id',"!=",$user_id)->where('user_type',4)->pluck('token')->all();
        }


        if (count($tokens_android) == 0 && count($tokens_ios) == 0) {
                //Log::info('no token found');
            return false;
        }

        if (isset($tokens_ios) && !empty($tokens_ios)){
            $ios_fields = array(
                'registration_ids' => $tokens_ios,
                'data' => $data,
                'notification' => array(
                    "title" => $data['title'],
                    "body" => $data['message'],
                   // "image" => $data['image'],
                    "priority" => "high",
                    "sound" => "default",
                )
            );
            sendNotification($ios_fields,"ios");
        }
        elseif (isset($tokens_android) && !empty($tokens_android)){
            $android_fields = array(
                'registration_ids' => $tokens_android,
                'data' => $data,
                'notification' => array(
                    "title" => $data['title'],
                    "body" => $data['message'],
                   // "image" => $data['image'],
                    "priority" => "high",
                    "sound" => "default",
                )
            );
            sendNotification($android_fields,"android");
        }

        return true;
    }
}

function sendNotification($data,$type){
    $api_key = env('ANDROID_NOTIFICATION_KEY');
    if($type == "ios"){
        $api_key = env('IOS_NOTIFICATION_KEY');
    }
    $headers = array('Authorization: key=' . $api_key, 'Content-Type: application/json');
    $url = 'https://fcm.googleapis.com/fcm/send';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    curl_close($ch);

    $data = explode(':', $result);
    $sucess = explode(",", $data[2]);

    return true;
}

// use Illuminate\Contracts\Database\Eloquent\Builder;
// return $query->orderBy('id', 'DESC')->paginate(10)->withQueryString()->through(function ($item) {
//     $item->id = $item->id;
//     $item->cancel_reason = $item->cancel_reason;
//     $item->cancel_comment = $item->cancel_comment;
//     $item->status = $item->status;
//     $item->created_at = $item->created_at;
//     $item->order_code = $item->order->order_code;
//     return $item;
// });


