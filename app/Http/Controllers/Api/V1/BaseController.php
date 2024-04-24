<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponseWithData($data, $message)
    {
        return response()->json(array('success'=>true,'status_code' => 200, 'message' => $message, 'data' => $data),200);
    }

    public function sendResponseSuccess($message)
    {
        return response()->json(array('success'=>true,'status_code' => 200, 'message' => $message),200);
    }

    public function sendError($httpCode,$error = [], $message, $errorMessages = [])
    {
        return response()->json(array('success'=>false,'status_code' => $httpCode, 'error' => $error, 'message' => $message, 'data' => $errorMessages),$httpCode);
    }
}
