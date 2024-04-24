<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PostBanner;
use App\Models\PostBannerConfig;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BannerController extends BaseController
{
    public $payload;

    public function __construct()
    {
        $token = JWTAuth::parseToken()->getToken();
        $this->payload = JWTAuth::decode($token);
    }

    public function banner_list()
    {
        $society_id = $this->payload['society_id'];
        if($society_id == ""){
            return $this->sendError(400,'Society Not Found.', "Not Found", []);
        }

        $banners = PostBanner::where('estatus',1)->where('society_id',$society_id)->get();
        $banner_arr = array();
        foreach ($banners as $banner) {
            $temp['post_banner_id'] = $banner->post_banner_id;
            $temp['banner_url'] = $banner->banner_url;
            array_push($banner_arr, $temp);
        }

        $data['banners'] = $banner_arr;
        return $this->sendResponseWithData($data, "All Banner Retrieved Successfully.");
    }

    public function set_banner_config(Request $request)
    {
        $block_flat_id = $this->payload['block_flat_id'];
        if($block_flat_id == ""){
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
       
        $rules = [
            'banner_for' => 'required|in:1,2',
            'society_member_id' => 'required_if:banner_for,2|int',
            'business_profile_id' => 'required_if:banner_for,1|int',
            'is_display_mobile_no' => 'required|in:1,2',
            'is_display_address' => 'required|in:1,2',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(422,$validator->errors(), "Validation Errors", []);
        }

        // Create a new record in post_banner_config table
        $config = PostBannerConfig::where('block_flat_id',$block_flat_id)->first();
        if(!$config){
            $config = new PostBannerConfig();
        }
        $config->block_flat_id = $block_flat_id;
        $config->create_banner_for = $request->banner_for;
        $config->master_item_id = ($request->banner_for == 1) ? $request->business_profile_id : $request->society_member_id;
        $config->is_display_mobile_no = $request->is_display_mobile_no;
        $config->is_display_address = $request->is_display_address;
        $config->updated_by = Auth::user()->user_id;
        $config->save();

        return $this->sendResponseSuccess("Banner Config Set Successfully");
    }
    

    public function get_banner_config()
    {
        $block_flat_id = $this->payload['block_flat_id'];
        if($block_flat_id == ""){
            return $this->sendError(400,'Flat Not Found.', "Not Found", []);
        }
        
        $config = PostBannerConfig::where('estatus',1)->where('block_flat_id',$block_flat_id)->first();
        if (!$config){
            return $this->sendError(404,"You can not get this config", "Invalid config", []);
        }
        $data = array();
        $temp['banner_for'] = $config->create_banner_for;
        $temp['business_profile_id'] = $config->create_banner_for==1?$config->master_item_id:0;
        $temp['society_member_id'] = $config->create_banner_for==2?$config->master_item_id:0;
        $temp['is_display_mobile_no'] = $config->is_display_mobile_no;
        $temp['is_display_address'] = $config->is_display_address;
        array_push($data, $temp); 
        return $this->sendResponseWithData($data, "Get Banner Config Successfully.");
    }
}
