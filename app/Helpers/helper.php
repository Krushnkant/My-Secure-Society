<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Config;
use App\Models\Settings;
use Illuminate\Support\Str;

class helper
{
    
    public static function MailSending($template, $data, $to, $sub){
		$fromEmail = env('MAIL_USERNAME');
		$fromName = env('MAIL_USERNAME');
        $settings =  ['logo'=>"demo.png"];
        $data = array('setting'=> $settings,'data'=>$data);
		Mail::send($template, $data, function($message) use ($fromEmail, $fromName, $to, $sub) {
         $message->from($fromEmail,$fromName);
         $message->to($to);
         $message->subject($sub);
      });
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
        ];
    }
    

}
