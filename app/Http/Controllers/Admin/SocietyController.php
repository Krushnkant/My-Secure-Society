<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Country;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SocietyController extends Controller
{
    public function index()
    {
        $countries = Country::get();
        return view('admin.society.list', compact('countries'));
    }

    public function listdata(Request $request)
    {

        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'ASC';

        // get data from products table
        $query = Society::select('*')->with('city','state','country');
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('society_name', 'like', "%" . $search . "%");
        });

        $orderByName = 'society_name';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'society_name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $data = $query->skip($skip)->take($pageLength)->get();
        return response()->json(["draw" => $request->draw, "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $data], 200);
    }

    public function addorupdate(Request $request)
    {
        $messages = [
            'society_name.required' => 'Please provide a society name',
            'street_address1.required' => 'Please provide a street address',
            'landmark.required' => 'Please provide a landmark',
            'pin_code.required' => 'Please provide a pin code',
            'city_id.required' => 'Please provide a city',
            'latitude.required' => 'Please provide a latitude',
            'longitude.required' => 'Please provide a longitude',
            'state_id.required' => 'Please provide a state',
            'country_id.required' => 'Please provide a country',
        ];
        $validator = Validator::make($request->all(), [
            'society_name' => 'required|max:100',
            'street_address1' => 'required|max:255',
            'street_address2' => 'max:255',
            'landmark' => 'required|max:50',
            'pin_code' => 'required|numeric',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city_id' => 'required',
            'state_id' => 'required',
            'country_id' => 'required',
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'failed']);
        }
        if (!isset($request->id)) {
            $society = new Society();
            $society->created_by = Auth::user()->user_id;
            $action = "add";
        } else {
            $society = Society::find($request->id);
            if (!$society) {
                return response()->json(['status' => '400']);
            }
            $action = "update";
        }
        $society->society_name = $request->society_name;
        $society->street_address1 = $request->street_address1;
        $society->street_address2 = $request->street_address2;
        $society->landmark = $request->landmark;
        $society->pin_code = $request->pin_code;
        $society->latitude = $request->latitude;
        $society->longitude = $request->longitude;
        $society->city_id = $request->city_id;
        $society->state_id = $request->state_id;
        $society->country_id = $request->country_id;
        $society->updated_by = Auth::user()->user_id;
        $society->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }
    public function edit($id)
    {
        $society = Society::find($id);
        return response()->json($society);
    }
    public function delete($id)
    {
        $society = Society::find($id);
        if ($society) {
            $block = Block::where('society_id', $id)->exists();
            if ($block) {
                return response()->json(['status' => '300', 'message' => 'Cannot delete society. It is associated with one or more society block.']);
            }
            $society->estatus = 3;
            $society->save();
            $society->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changestatus($id)
    {
        $society = Society::find($id);
        if ($society->estatus == 1) {
            $society->estatus = 2;
            $society->save();
            return response()->json(['status' => '200', 'action' => 'deactive']);
        }
        if ($society->estatus == 2) {
            $society->estatus = 1;
            $society->save();
            return response()->json(['status' => '200', 'action' => 'active']);
        }
    }

    public function multipledelete(Request $request)
    {
        $ids = $request->input('ids');
        $societies = Society::whereIn('society_id', $ids)->get();
        foreach ($societies as $societie) {
            $block = Block::where('society_id', $societie->society_id)->exists();
            if (!$block) {
                $societie->estatus = 3;
                $societie->save();
                $societie->delete();
            }
        }
        return response()->json(['status' => '200']);
    }
}
