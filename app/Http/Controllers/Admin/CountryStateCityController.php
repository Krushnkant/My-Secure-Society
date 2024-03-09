<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{State,City};

class CountryStateCityController extends Controller
{
    public function getState(Request $request)
    {
        $data['states'] = State::where("country_id",$request->country_id)->get(["state_name","state_id"]);
        return response()->json($data);
    }
    public function getCity(Request $request)
    {
        $data['cities'] = City::where("state_id",$request->state_id)->get(["city_name","city_id"]);
        return response()->json($data);
    }
}
