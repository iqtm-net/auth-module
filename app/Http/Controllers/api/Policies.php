<?php

namespace App\Http\Controllers\api;

use App\Policy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;
use Session;
use Illuminate\Support\Facades\Storage;

class Policies extends Controller
{
    
    public function Fetch(){
        $fetch = Policy::all();
        return ResultNoSB($fetch);
    }

    public function Put(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|String|max:200',
            'body' => 'required|String',
        ]);

        if($validator->fails()){  return Result(Null, 400, $validator->errors()); }

        $new = new Policy;
        $new->title = $request->get('title');
        $new->body = $request->get('body');
        $new->save();

        return Result('seccuss');
    }

    public function Delete(Request $request){

        $validator = Validator::make($request->all(), [ 'id' => 'required|Numeric']);

        if($validator->fails()){  return Result(Null, 400, $validator->errors()); }

        $find = Policy::find($request->get('id'));

        if(!$find){ return Result(Null, 404, 'Id Not Found'); }

        $find->delete();

        return Result('seccuss');
    }
}
