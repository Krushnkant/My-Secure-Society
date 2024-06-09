<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{


    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token expired'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token invalid'), 401);
            } else {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token absent'), 401);
            }
        }


        $designation_id =  getResidentDesignationId();
        $designation = getResidentDesignation($designation_id);
        $v1 = 'api/v1/';
        $message = 'You are not authorized';

        //if ($designation == "Society Member") {
            // if($request->route()->uri()== $v1.'users/flat/list' && is_view_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message1' => $message), 401);
            // }
            // if($request->route()->uri()==$v1.'users/flat/save' && is_add_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message2' => $message), 401);
            // }if($request->route()->uri()==$v1.'users/flat/delete' && is_delete_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message3' => $message), 401);
            // }



            if(!isset($request->calling_by)){
                return response()->json(array('success' => false, 'status_code' => 422, 'error' => 'Validation Error',  'message' => "The calling by field is required."), 422);
            }

            if($request->calling_by != 1 && $request->calling_by != 2){
                return response()->json(array('success' => false, 'status_code' => 422, 'error' => 'Validation Error',  'message' => "calling by field is invalid"), 422);
            }

            if ($request->route()->uri() == $v1 . 'family_member/list' && is_view_resident(2) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'family_member/save' && $request->calling_by == 1) {
                if ($request->user_id == 0 && is_add_resident(2) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->user_id > 0 && is_edit_resident(2) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'family_member/delete' && is_delete_resident(2) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'banner/list' && is_view_resident(3) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'banner/config/get' && is_view_resident(4) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'banner/config/set' && is_EDIT_resident(4) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'folder/list' && is_view_resident(5) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'folder/get' && is_view_resident(5) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'folder/save' && $request->calling_by == 1) {
                if ($request->folder_id == 0 && is_add_resident(5) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->folder_id > 0 && is_edit_resident(5) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'folder/delete' && is_delete_resident(5) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'document/list' && is_view_resident(6) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'document/get' && is_view_resident(6) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'document/save' && $request->calling_by == 1) {
                if ($request->folder_id == 0 && is_add_resident(6) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->folder_id > 0 && is_edit_resident(6) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'document/delete' && is_delete_resident(6) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'resident/list' && is_view_resident(7) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'resident/get' && is_view_resident(7) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'resident/change_status' && is_edit_resident(7) == 0 && $request->calling_by == 1) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'resident/list' && is_view_resident(55) == 0 && $request->calling_by == 2) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'resident/get' && is_view_resident(55) == 0 && $request->calling_by == 2) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'resident/change_status' && is_edit_resident(55) == 0 && $request->calling_by == 2) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'announcement/list' && is_view_resident(8) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'announcement/get' && is_view_resident(8) == 0 ) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'announcement/save' ) {
                    if ($request->folder_id == 0 && is_add_resident(8) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->folder_id > 0 && is_edit_resident(8) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }

                if ($request->route()->uri() == $v1 . 'announcement/delete' && is_delete_resident(57) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'announcement/list' && is_view_resident(57) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'announcement/get' && is_view_resident(57) == 0 ) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'announcement/save' ) {
                    if ($request->folder_id == 0 && is_add_resident(57) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->folder_id > 0 && is_edit_resident(57) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }

                if ($request->route()->uri() == $v1 . 'announcement/delete' && is_delete_resident(57) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if ($request->route()->uri() == $v1 . 'daily_post/list' && is_view_resident(10) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'daily_post/get' && is_view_resident(10) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'daily_post/save') {
                if ($request->post_id == 0 && is_add_resident(10) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->post_id > 0 && is_edit_resident(10) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'daily_post/change_status' && is_edit_resident(10) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'amenity/list' && is_view_resident(11) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/get' && is_view_resident(11) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/save') {
                    if ($request->amenity_id == 0 && is_add_resident(11) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->amenity_id > 0 && is_edit_resident(11) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'amenity/delete' && is_delete_resident(11) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'amenity/list' && is_view_resident(58) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/get' && is_view_resident(58) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/save') {
                    if ($request->amenity_id == 0 && is_add_resident(58) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->amenity_id > 0 && is_edit_resident(58) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'amenity/delete' && is_delete_resident(58) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }


            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'amenity/booking/list' && is_view_resident(12) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/booking/create' && is_add_resident(12) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/booking/change_status' && is_edit_resident(12) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'amenity/booking/list' && is_view_resident(59) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/booking/create' && is_add_resident(59) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'amenity/booking/change_status' && is_edit_resident(59) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'emergency_alert/create' && is_add_resident(13) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_alert/list' && is_view_resident(13) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_alert/delete' && is_delete_resident(13) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'emergency_alert/create' && is_add_resident(60) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_alert/list' && is_view_resident(60) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_alert/delete' && is_delete_resident(60) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'emergency_contact/save') {
                    if ($request->contact_type != 3) {
                        if ($request->contact_id == 0 && is_add_resident(14) == 0) {
                            return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                        }
                        if ($request->contact_id > 0 && is_edit_resident(14) == 0) {
                            return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                        }
                    }
                }
                if ($request->route()->uri() == $v1 . 'emergency_contact/list' && is_view_resident(14) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_contact/delete' && is_delete_resident(14) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'emergency_contact/save') {
                    if ($request->contact_type != 3) {
                        if ($request->contact_id == 0 && is_add_resident(61) == 0) {
                            return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                        }
                        if ($request->contact_id > 0 && is_edit_resident(61) == 0) {
                            return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                        }
                    }
                }
                if ($request->route()->uri() == $v1 . 'emergency_contact/list' && is_view_resident(61) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'emergency_contact/delete' && is_delete_resident(61) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }


            if ($request->route()->uri() == $v1 . 'business_profile/save') {
                if ($request->contact_id == 0 && is_add_resident(18) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->contact_id > 0 && is_edit_resident(18) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'business_profile/list' && is_view_resident(18) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'business_profile/get' && is_view_resident(18) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'business_profile/delete' && is_delete_resident(18) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'visitor/new/save' && is_add_resident(29) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'visitor/list' && is_view_resident(29) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'visitor/change_status' && is_edit_resident(29) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'visitor/new/save' && is_add_resident(73) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'visitor/list' && is_view_resident(73) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'visitor/change_status' && is_edit_resident(73) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'gatepass/save') {

                    if ($request->contact_id == 0 && is_add_resident(28) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(28) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }

                }
                if ($request->route()->uri() == $v1 . 'gatepass/list' && is_view_resident(28) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'gatepass/get' && is_view_resident(28) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'gatepass/delete' && is_delete_resident(28) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'gatepass/save') {

                    if ($request->contact_id == 0 && is_add_resident(72) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(72) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }

                }
                if ($request->route()->uri() == $v1 . 'gatepass/list' && is_view_resident(72) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'gatepass/get' && is_view_resident(72) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'gatepass/delete' && is_delete_resident(72) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/new_item/save') {
                    if ($request->contact_id == 0 && is_add_resident(30) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(30) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/list' && is_view_resident(30) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/get' && is_view_resident(30) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/change_status' && is_edit_resident(30) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/new_item/save') {

                    if ($request->contact_id == 0 && is_add_resident(74) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(74) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/list' && is_view_resident(74) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/get' && is_view_resident(74) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'delivered_at_gate/courier/change_status' && is_edit_resident(74) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/save') {

                    if ($request->contact_id == 0 && is_add_resident(31) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(31) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/list' && is_view_resident(31) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/get' && is_view_resident(31) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/delete' && is_delete_resident(31) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/save') {
                    if ($request->contact_id == 0 && is_add_resident(75) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(75) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/list' && is_view_resident(75) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/get' && is_view_resident(75) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'daily_help/service_provider/delete' && is_delete_resident(75) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if ($request->route()->uri() == $v1 . 'daily_help/service_provider/add_to_flat') {

                if ($request->contact_id == 0 && is_add_resident(32) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->contact_id > 0 && is_edit_resident(32) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'daily_help/service_provider/delete_flat' && is_delete_resident(32) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'department/save') {
                if ($request->contact_id == 0 && is_add_resident(51) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->contact_id > 0 && is_edit_resident(51) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'department/list' && is_view_resident(51) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'department/delete' && is_delete_resident(51) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }

            if ($request->route()->uri() == $v1 . 'duty_area/save') {
                if ($request->contact_id == 0 && is_add_resident(66) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->contact_id > 0 && is_edit_resident(66) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }
            if ($request->route()->uri() == $v1 . 'duty_area/list' && is_view_resident(66) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'duty_area/get' && is_view_resident(66) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }
            if ($request->route()->uri() == $v1 . 'duty_area/delete' && is_delete_resident(66) == 0) {
                return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
            }


            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'staff_member/save') {

                    if ($request->contact_id == 0 && is_add_resident(23) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(23) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'staff_member/list' && is_view_resident(23) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/get' && is_view_resident(23) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/delete' && is_delete_resident(23) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'staff_member/save') {
                    if ($request->contact_id == 0 && is_add_resident(67) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(67) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'staff_member/list' && is_view_resident(67) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/get' && is_view_resident(67) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/delete' && is_delete_resident(67) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }


            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/save') {

                    if ($request->contact_id == 0 && is_add_resident(24) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(24) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/list' && is_view_resident(24) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/get' && is_view_resident(24) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/delete' && is_delete_resident(24) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/save') {
                    if ($request->contact_id == 0 && is_add_resident(68) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                    if ($request->contact_id > 0 && is_edit_resident(68) == 0) {
                        return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                    }
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/list' && is_view_resident(68) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/get' && is_view_resident(68) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/duty_area/delete' && is_delete_resident(68) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }

            if($request->calling_by == 1){
                if ($request->route()->uri() == $v1 . 'staff_member/fill_attendance' && is_add_resident(25) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/attendance/list' && is_view_resident(25) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
            }else{
                if ($request->route()->uri() == $v1 . 'staff_member/fill_attendance' && is_add_resident(69) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }
                if ($request->route()->uri() == $v1 . 'staff_member/attendance/list' && is_view_resident(69) == 0) {
                    return response()->json(array('success' => false, 'status_code' => 401, 'error' => 'Unauthorized',  'message' => $message), 401);
                }

            }



            return $next($request);

        // }else{

        // }


        return $next($request);
    }
}
