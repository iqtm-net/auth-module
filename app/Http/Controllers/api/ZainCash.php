<?php

    namespace App\Http\Controllers\api;


    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;
    use Session;
    use App\Credits_order;
    use App\CustomClass\JWT;

    class ZainCash extends Controller
    {
         
        public function NewPayment(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1000',
            ]);

            if($validator->fails()){ return Result(Null, 400, $validator->errors()); }


            ini_set('precision', 15);

            //ENTER THESE VALUES BY YOURSELF OR USE ANOTHER VARIABLES TO LINK THEM

            // ----------------- Merchant Details --------------------------
            //Your wallet number   (ZainCash IT will provide it for you)
            $msisdn=9647800285011;

            //Secret   (ZainCash IT will provide it for you)
            $secret='$2y$10$DOypWBYnCOWIknsKM5GZPOKgrTdfZZvOWnmSSCQsOlo1A/Z0YdoRi';

            //Merchant ID   (ZainCash IT will provide it for you) 5dac4a31c98a8254092da3d8
            $merchantid='5ee757edb1e2e777aba2f260';

            //Test credentials or Production credentials (true=production , false=test)
            $production_cred=true;

            //Language 'ar'=Arabic     'en'=english
            $language='ar';

            //==============================================================================

            $amount = $request->get('amount');

            //Type of service you provide, like 'Books', 'ecommerce cart', 'Hosting services', ...
            $service_type="WordPress Cart";

            //Order id, you can use it to help you in tagging transactions with your website IDs, if you have no order numbers in your website, leave it 1
            //Variable Type is STRING, MAX: 512 chars
            $order_id="Bill_1234567890";

            //after a successful or failed order, the user will redirect to this url
            $redirection_url='http://ihodhod.com/api/ZainCash/response';

            /* ------------------------------------------------------------------------------
            Notes about $redirection_url:
            in this url, the api will add a new parameter (token) to its end like:
            https://example.com/redirect.php?token=XXXXXXXXXXXXXX
            ------------------------------------------------------------------------------  */

            //building data
            /*$data = [
            'amount'  => $amount,
            'serviceType'  => $service_type,
            'msisdn'  => $msisdn,
            'orderId'  => $order_id,
            'redirectUrl'  => $redirection_url,
            'DeliverId'  => Auth::guard('delivers')->user()->id,
            'DeliverGD'  => Auth::guard('delivers')->user()->GD,
            'iat'  => time(),
            'exp'  => time()+60*60*4
            ];*/

            $data = [
            'amount'  => $amount,
            'serviceType'  => $service_type,
            'msisdn'  => $msisdn,
            'orderId'  => $order_id,
            'redirectUrl'  => $redirection_url,
            'iat'  => time(),
            'exp'  => time()+60*60*4
            ];

            //Encoding Token
            $newtoken = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secret ,'HS256'
            );

            //echo
            $newtoken;

            //Check if test or production mode
            if($production_cred){
                $tUrl = 'https://api.zaincash.iq/transaction/init';
                $rUrl = 'https://api.zaincash.iq/transaction/pay?id=';
            }else{
                $tUrl = 'https://test.zaincash.iq/transaction/init';
                $rUrl = 'https://test.zaincash.iq/transaction/pay?id=';
            }

            //POSTing data to ZainCash API
            $data_to_post = array();
            $data_to_post['token'] = urlencode($newtoken);
            $data_to_post['merchantId'] = $merchantid;
            $data_to_post['lang'] = $language;

            $options = array(
                'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data_to_post),
                ),
            );

            $context  = stream_context_create($options);
            $response = file_get_contents($tUrl, false, $context);

            //Parsing response
            $array = json_decode($response, true);
            $transaction_id = $array['id'];
            $newurl=$rUrl.$transaction_id;

            //$request->session()->put('OrderId', $transaction_id);
            //$request->session()->put('DeliverId', user()->id);
            //$request->session()->put('Amount', $amount);

            return Result(compact('transaction_id'), 200);
        }

        public function Result(Request $request)
        {
            $secret='$2y$10$DOypWBYnCOWIknsKM5GZPOKgrTdfZZvOWnmSSCQsOlo1A/Z0YdoRi';

            if ($request->has('response')){

                $token = explode("?token=", $request->get('response'))[1];
                 //you can decode the token by this PHP code:
                $result= JWT::decode($token, $secret, array('HS256'));
                $result= (array) $result;

                //And to check for status of the transaction, use $result['status'], like this:
                if ($result['status']=='success'){

                    $new = new Credits_order;
                    $new->deliver_id = $request->get('member_id');
                    $new->transaction_id = $request->get('transaction_id');
                    $new->amount = $request->get('amount');
                    $new->phone_number = $request->get('phone_number');
                    $new->status = 0;
                    $new->save();

                    return Result("done", 200);

                    Eventing(
                    "New credit invoice from deliver_id ".$request->get('member_id')." amount (".$request->get('amount').")" ,
                    "/credit_orders_pending/1");
                }

                if ($result['status']=='failed'){ return Result($result['msg'], 400); }
            }
            else {
                return Result("Operation has been canceled", 202);
            }
        }


    }
