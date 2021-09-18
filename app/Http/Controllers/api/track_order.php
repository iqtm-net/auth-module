<?php

    namespace App\Http\Controllers\api;

    use App\User_order;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;
    use Illuminate\Support\Facades\Auth;

    class track_order extends Controller
    {
         

        public function index(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'tracking_number' => 'required|string',
            ]);
    
            if($validator->fails()){ Result(Null, 400, $validator->errors()); }
            
            $get_res = User_order::where('track_code', $request->get('tracking_number'))->first();
                        
            if(!$get_res){
                return ResultNoSB("Invalid Track Number", 404);
            }

            return Result($get_res, 200);

        }


    }
