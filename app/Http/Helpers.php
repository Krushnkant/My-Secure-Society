<?php 

namespace App\Http;

use Mail;
use Config;
use App\Models\Settings;
use Illuminate\Support\Str;

class Helpers{
	
	public static function MailSending($template, $data, $to, $sub){
		$fromEmail = env('MAIL_USERNAME');
		$fromName = env('MAIL_USERNAME');
        $settings = [];
        $data = array('setting'=> $settings,'data'=>$data);
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
}