<?php

    namespace App\Http\Controllers\api;
    use App\Report;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;
    class Reporting extends Controller
    {
        

        public function Reporting1(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:20',
                'phone_number' => 'required|numeric|digits_between:0,15',
                'track_code' => 'string|max:20',
                'describtion' => 'required|string|max:1000',
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }

            $posts = Report::create($request->all());
            $seccus = "seccuss";

            EventingPublic("guest",0,"New Feedback From (".$request->get('name').")","reports/1");

            return Result(compact("seccus"), 200);

        }

        public function reports(Request $request)
        {
            $get = Report::latest()->paginate(10);
            return response()->json($get);

        }

    }
