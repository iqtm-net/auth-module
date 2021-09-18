<?php

    namespace App\Http\Controllers\api\Authintication;

    use App\User;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Config;
    use Illuminate\Support\Str;

    class UserAuth extends Controller
    {
        public function authenticate(Request $request)
        {   

            auth()->shouldUse("users");

            $credentials = $request->only('email', 'password');

            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

            return Result(compact('token'), 200);
        }

        public function register(Request $request)
        {

            auth()->shouldUse("users");

            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|Numeric|max:100000000000000|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }

            //Send Code Sys
            $code = Str::random(7);

            $username = 'levantain13';
            $password = 'hgthvs1234@';

            $messages = array(
              array('to'=>'+964'.$request->get('phone_number'), 'body'=>'Your Code Is : '.$code)
            );  

            $result = send_message( 
                json_encode($messages), 
                'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', $username, $password 
            );

            ////////////////////////////////////////////
                $seccuss = "seccuss, a code has been sent to your mobile number";

                $sentcode = Hash::make($code);
            
                return Result(compact('seccuss','sentcode','code'), 200);
            ////////////////////////////////////////////

            if ($result['http_status'] != 201) {

                return Result($result['server_response'], $result['http_status']);
            }

            else {
                $seccuss = "seccuss, a code has been sent to your mobile number";

                $sentcode = Hash::make($code);
            
                return Result(compact('seccuss','sentcode','code'), 200);
            }

            
        }

        public function confirm_code(Request $request)
        {   

            $validator = Validator::make($request->all(), [
                'enteredcode' => 'required|string|max:255',
                'sentcode' => 'required|string|max:255',
                'full_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|Numeric|max:100000000000000|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }


            if (Hash::check($request->get('enteredcode'), $request->get('sentcode'))) {

                $seccuss = "Your Account Has Been Confirmed Succussfuly";

                $user = User::create([
                    'full_name' => $request->get('full_name'),
                    'address' => $request->get('address'),
                    'phone_number' => $request->get('phone_number'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($request->get('password')),
                ]);

                $token = JWTAuth::fromUser($user);

                return Result(compact('seccuss','token'), 201);
            }
            else{

                $error = "Wrong Code";

                return Result(compact('error'), 403);
            }

        }

    }