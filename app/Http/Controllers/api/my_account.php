<?php

    namespace App\Http\Controllers\api;

    use App\Http\Controllers\Controller;
    use Tymon\JWTAuth\JWTAuth;
    use Config;
    use Session;
    use App\User_order;
    use App\Orders_group;
    use Illuminate\Support\Facades\DB;
    use Maatwebsite\Excel\Facades\Excel;
    use Illuminate\Support\Arr;
    use App\Exports\ClientStatistic;


    class my_account extends Controller
    {
         
        public function me()
        {   
            
            $me = user();
            $me['premissions'] = (Arr::has($me, 'premissions')) ? json_decode($me['premissions']) : null;
            
            // $WaitingUnAccepted = 0;
            // $WaitingAccepted = 0; 
            // $DelayLocal = 0;
            // $Pending = 0;
            // $Returned = 0;  
            // $Delivered = 0; 
            
            // $Orders = User_order::where('user_id', user()->id)
            // ->where('account_type', user_role_number())
            // ->where('in_cart', 0)
            // ->get();

            // ->map(function ($Order) use (&$WaitingUnAccepted, &$WaitingAccepted, &$Pending, &$Returned, &$Delivered){

            //     $Group = Orders_group::where('id', $Order->OrderGroupe_Id)->first(); 
  
            //     if($Group){
            //         if($Order->status == "waiting" && $Group->accepted)  { $WaitingAccepted++; }
            //         elseif($Order->status == "waiting" && !$Group->accepted) { $WaitingUnAccepted++; }
            //         elseif($Order->status == "pending") { $Pending++; }
            //         elseif(in_array($Order->status, ['ReturnedToDeliver', 'ReturnedToClient'])) { $Returned++; }
            //         elseif($Order->status == "delivered") { $Delivered++; }
            //     }

            //     return $Order;
            // }); 
            
            // $me['WaitingUnAccepted'] = $WaitingUnAccepted;
            // $me['WaitingAccepted'] = $WaitingAccepted;
            // $me['Pending'] = $Pending;
            // $me['Returned'] = $Returned;
            // $me['Delivered'] = $Delivered;
            
            

            return Result($me, 200);
            
            
        }

        public function MyId()
        {
            return user()->id;
        }

        public function statistic($client_unique_code, $download)
        {
            $type = array('admins', 'receivers', 'users', 'stores', 'delivers', 'taxis', 'companies');

            foreach ($type as $key) {

                $check = DB::table($key)->where('Code', $client_unique_code)->first();

                if ($check) {

                    $get_orders = User_order::where('user_id', $check->id)
                    ->where('account_type', user_role_number($key))
                    ->whereIn('status', ['ReturnedToDeliver', 'ReturnedToClient','delivered'])
                    ->get();

                    if($get_orders->count() <= 0){ return Result("You don't have complete orders yet !", 400); }

                    return ($download == 'true') ? Excel::download(new ClientStatistic($get_orders), 'احصائية الطلبات'.'.xlsx') : $get_orders;
                }
            }

            return Result("Not Found", 404);
            
        }
        
    }
