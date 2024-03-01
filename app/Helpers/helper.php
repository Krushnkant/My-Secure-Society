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

}
