<?php

namespace App\Http;

use Mail;
use Config;
use App\Models\Settings;
use Illuminate\Support\Str;

class Helpers{

	public static function MailSending($template, $data, $to, $sub){
		$fromEmail = env('MAIL_USERNAME');
		$fromName = env('MAIL_FROM_NAME');
        $data = array('company_name'=> env('MAIL_FROM_NAME'),'data'=>$data);
		Mail::send($template, $data, function($message) use ($fromEmail, $fromName, $to, $sub) {
         $message->from($fromEmail,$fromName);
         $message->to($to);
         $message->subject($sub);
      });
		// dump('Mail Send Successfully');
	}

	public static function UploadImage($image, $path){
        $imageName = Str::random().'.'.$image->getClientOriginalExtension();
        // dump($imageName);
        // $path = Storage::disk('public')->putFileAs($path, $image,$imageName);
        $path = $image->move($path, $imageName);
        // dump($path);
        if($path == true){
            return $imageName;
        }else{
            return null;
        }
    }

    public static function getModulesArray()
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
            13 => 'Service Vendor',
            14 => 'Daily Help Service',
        ];
    }


}
