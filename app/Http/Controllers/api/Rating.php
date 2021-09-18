<?php

namespace App\Http\Controllers\api;

use App\User_order;
use App\Rate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;
class Rating extends Controller
{
     
    public function Rating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric',
            'rater_id' => 'required|numeric',
            'rated_id' => 'required|numeric',
            'section' => 'required'
        ]);

        if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

        $requestData = $request->all();
        //$update = User_order::find($requestData['order_id']);
        //if (!$update){ return Result(Null, 400, "Order Not Found"); }

        $new = new Rate;
        $new->rater_id = $requestData['rater_id'];
        $new->rated_id = $requestData['rated_id'];
        $new->section = $requestData['section'];
        $new->rate = $requestData['rate'];
        
        //$update->save();

        return Result("succuss");
    }

    public function GetRate($Section, $rated_id)
    {
        
        $get = Rate::where('section', $Section)->where('rated_id', $rated_id)->pluck('rate')->all();
        $count = array_count_values($get);
        arsort($count);
        $HighestRate = key($count);
        return Result($HighestRate);
    }
}   
