<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\PostBanner;
use Illuminate\Http\Request;
use JWTAuth;

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
            return $this->sendError('Society Not Found.', "Not Found", []);
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

    public function banner_config_list()
    {
        $society_member_id = $this->payload['society_member_id'];
        if($society_member_id == ""){
            return $this->sendError('Society Member Not Found.', "Not Found", []);
        }
        
        $banners = PostBanner::where('estatus',1)->get();
        $banner_arr = array();
        foreach ($banners as $banner) {
            $temp['post_banner_id'] = $banner->post_banner_id;
            $temp['banner_url'] = $banner->banner_url;
            array_push($banner_arr, $temp);
        }

        $data['banners'] = $banner_arr;
        return $this->sendResponseWithData($data, "All Banner Retrieved Successfully.");
    }
}
