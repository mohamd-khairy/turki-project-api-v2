<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryPeriodController extends Controller
{
    /**
     * @param Request $request
     */
    public function getAll()
    {
        return response()->json(['success' => true ,'data'=> DeliveryPeriod::where('is_active', '1')->get(),
        'message'=> 'retrieved successfully', 'description'=> '', 'code'=>'200'],200);
    }

    public function getById($dpId)
    {
        if(!is_numeric($dpId))
            return response()->json(['message' => 'bad value!'], 400);

        $dp = DeliveryPeriod::find($dpId);

        return response()->json(['success' =>  true ,'data'=> $dp,
            'message'=> 'retrieved successfully', 'description'=> '', 'code'=>'200'],200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $validateDate = $request->validate([
            'name_ar' => 'required|max:255',
            'name_en' => 'required|max:255',
            'time_hhmm' => array('required', "regex:(^(?:\d|[01]\d|2[0-3]):[0-5]\d$)")
        ]);

        $validateDate['is_active'] = 1;
        $dp = DeliveryPeriod::create($validateDate);

        return response()->json(['success' => true ,'data'=> $dp,
          'message'=> 'Successfully Added!', 'description'=> '', 'code'=> 200 ],200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete($dpId)
    {
        if(!is_numeric($dpId))
            return response()->json(['message' => 'bad value!'], 400);

        $dp = DeliveryPeriod::find($dpId)->delete();

        return response()->json(['message' => 'Successfully Deleted!'], 200);
    }


    public function update(Request $request)
    {
        $validateDate = $request->validate([
            'id' => 'required|exists:delivery_periods,id',
            'name_ar' => 'required|max:255',
            'name_en' => 'required|max:255',
            'time_hhmm' => array('required', 'regex:(^([-+] ?)?[0-9]+(,[0-9]+)*$)'),
            'is_active' => 'required|boolean'
        ]);

        $dp = DeliveryPeriod::update($validateDate);

        return response()->json(['success' => true ,'data'=> $dp,
            'message'=> 'Successfully Added!', 'description'=> '', 'code'=> 200 ],200);
    }

}
