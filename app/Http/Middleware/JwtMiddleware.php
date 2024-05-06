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
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token expired'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token invalid'), 401);
            } else {
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized', 'message' => 'Token absent'), 401);
            }
        }


            $designation_id =  getResidentDesignationId();

            $v1 = 'api/v1/';
            $message = 'You are not authorized';

            // if($request->route()->uri()== $v1.'users/flat/list' && is_view_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message1' => $message), 401);
            // }
            // if($request->route()->uri()==$v1.'users/flat/save' && is_add_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message2' => $message), 401);
            // }if($request->route()->uri()==$v1.'users/flat/delete' && is_delete_resident(1) == 0){
            //     return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message3' => $message), 401);
            // }

            if($request->route()->uri()== $v1.'family_member/list' && is_view_resident(2) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()==$v1.'family_member/save'){
                if($request->user_id == 0 && is_add_resident(2) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
                if($request->user_id > 0 && is_edit_resident(2) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
            }if($request->route()->uri()==$v1.'family_member/delete' && is_delete_resident(2) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message6' => $message), 401);
            }

            if($request->route()->uri()== $v1.'banner/list' && is_view_resident(3) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }

            if($request->route()->uri()== $v1.'banner/config/get' && is_view_resident(4) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message1' => $message), 401);
            }if($request->route()->uri()==$v1.'banner/config/set' && is_EDIT_resident(4) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message2' => $message), 401);
            }

            if($request->route()->uri()== $v1.'folder/list' && is_view_resident(5) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'folder/get' && is_view_resident(5) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()==$v1.'folder/save'){
                if($request->folder_id == 0 && is_add_resident(5) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
                if($request->folder_id > 0 && is_edit_resident(5) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
            }if($request->route()->uri()==$v1.'folder/delete' && is_delete_resident(5) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message6' => $message), 401);
            }

            if($request->route()->uri()== $v1.'document/list' && is_view_resident(6) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'document/get' && is_view_resident(6) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()==$v1.'document/save'){
                if($request->folder_id == 0 && is_add_resident(6) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
                if($request->folder_id > 0 && is_edit_resident(6) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
            }if($request->route()->uri()==$v1.'document/delete' && is_delete_resident(6) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message6' => $message), 401);
            }

            if($request->route()->uri()== $v1.'resident/list' && is_view_resident(7) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'resident/get' && is_view_resident(7) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }

            if($request->route()->uri()== $v1.'announcement/list' && is_view_resident(8) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'announcement/get' && is_view_resident(8) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }

            if($request->route()->uri()== $v1.'daily_post/list' && is_view_resident(10) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'daily_post/get' && is_view_resident(10) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()==$v1.'daily_post/save'){
                if($request->post_id == 0 && is_add_resident(10) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
                if($request->post_id > 0 && is_edit_resident(10) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
            }if($request->route()->uri()==$v1.'daily_post/delete' && is_delete_resident(10) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message6' => $message), 401);
            }

            if($request->route()->uri()== $v1.'amenity/list' && is_view_resident(11) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()== $v1.'amenity/get' && is_view_resident(11) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }if($request->route()->uri()==$v1.'amenity/save'){
                if($request->amenity_id == 0 && is_add_resident(11) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
                if($request->amenity_id > 0 && is_edit_resident(11) == 0){
                    return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
                }
            }if($request->route()->uri()==$v1.'amenity/delete' && is_delete_resident(11) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message6' => $message), 401);
            }

            if($request->route()->uri()== $v1.'amenity/booking/list' && is_view_resident(12) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message4' => $message), 401);
            }
            if($request->route()->uri()==$v1.'amenity/booking/create' && is_view_resident(12) == 0){
                return response()->json(array('success'=>false,'status_code' => 401, 'error' => 'Unauthorized',  'message5' => $message), 401);
            }

            else{
                return $next($request);
            }


        return $next($request);
    }
}
