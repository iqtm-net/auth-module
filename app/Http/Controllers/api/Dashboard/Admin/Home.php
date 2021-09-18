<?php

namespace App\Http\Controllers\api\Dashboard\Admin;

use App\Deliver;
use App\User;
use App\Store;
use App\Taxi;
use App\User_order;
use App\Report;
use App\Orders_group;
use App\Withdraw_order;
use App\Orders_black_list;
use App\Member_stack;
use App\Models\HodHod_Taxi\Taxi_trip;
use App\Offers_order;
Use \Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use File;

class Home extends Controller
{

    public function index($dateFrom,$dateto)
    { 

        //================================================ Overview
        $orders = User_order::all()->count();
        $GiftOrders = Offers_order::all()->count();
        $TaxiRides = Taxi_trip::all()->count();
        $users = User::all()->count();
        $stores = Store::all()->count();
        $delivers = Deliver::all()->count();
        $taxis = Taxi::all()->count();
        $Orders_black_list = Orders_black_list::all()->count();
        $recieved_prices = User_order::select('recieved_price')->where('status', 'delivered')->get()->sum('recieved_price');
        $Deliver_Fee = User_order::select('Deliver_Fee')->where('status', 'delivered')->get()->sum('Deliver_Fee');
        $Reports = Report::latest()->paginate(5);
        $CountReports = Report::all()->count();
        $WithdrawOrders = Withdraw_order::all()->count();
        $Withdraw_balance = Withdraw_order::where('status', 1)->get()->sum('balance');
        $UnWithdraw_balance = Withdraw_order::where('status', 0)->get()->sum('balance');
        $Users_Stores_balance = Store::all()->sum('balance') + User::all()->sum('balance');

        //================================================= Statistic By Location
        $SendingStates = User_order::select('location_to_state')->distinct('location_to_state')->get()
        ->map(function ($Data) {

            $Data['location_to_state'] = $Data->location_to_state;
            $Data['Total_Orders'] = User_order::where('location_to_state', $Data->location_to_state)->get()->count();

            return $Data;  

        });

        $ReceivingStates = User_order::select('location_from_state')->distinct('location_from_state')->get()
        ->map(function ($Data) {

            $Data['location_from_state'] = $Data->location_from_state;
            $Data['Total_Orders'] = User_order::where('location_from_state', $Data->location_from_state)->get()->count();

            return $Data;  

        });

        //================================================= TOP 5

        $GetDelivers = array_filter(User_order::select('deliver_id')->distinct('deliver_id')->get()
        ->map(function ($Data) {

            $GetDeliver = Deliver::find($Data->deliver_id);

            if($GetDeliver){
                $Data['id'] = $Data->deliver_id;
                $Data['Deliver'] = $GetDeliver->first_name." ".$GetDeliver->last_name;
                $Data['Total_Orders'] = User_order::where('deliver_id', $Data->deliver_id)->get()->count();
                return $Data; 
            } 

        })->toArray());

        array_multisort(array_column($GetDelivers, 'Total_Orders'), SORT_DESC, $GetDelivers);
        $TopDelivers = array_slice($GetDelivers, 0, 5, true);  


        $GetUsers = array_filter(User_order::select('user_id','account_type')->where('account_type', 2)->distinct('user_id')->get()
        ->map(function ($Data) {

            $GetMember = User::find($Data->user_id);
            if($GetMember)
            {   
                $Data['id'] = $GetMember->id;
                $Data['Member'] = $GetMember->first_name." ".$GetMember->last_name;
                $Data['Total_Orders'] = User_order::where('user_id', $GetMember->id)->where('account_type', 2)->get()->count();
                return $Data; 
            }
 
        })->toArray());

        array_multisort(array_column($GetUsers, 'Total_Orders'), SORT_DESC, $GetUsers);
        $TopUsers = array_slice($GetUsers, 0, 5, true);
        

        $GetStores = array_filter(User_order::select('user_id','account_type')->where('account_type', 3)->distinct('user_id')->get()
        ->map(function ($Data) {

            $GetMember = User::find($Data->user_id);
            if($GetMember)
            {   
                $Data['id'] = $GetMember->id;
                $Data['Member'] = $GetMember->first_name." ".$GetMember->last_name;
                $Data['Total_Orders'] = User_order::where('user_id', $GetMember->id)->where('account_type', 3)->get()->count();
                return $Data; 
            }
 
        })->toArray());

        array_multisort(array_column($GetStores, 'Total_Orders'), SORT_DESC, $GetStores);
        $TopStores = array_slice($GetStores, 0, 5, true);
        
        //================================================ Other Charts
        for ($i=0; $i < 7; $i++) {
            $OrdersSubWeek[] = User_order::whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->count();
        }

        for ($i=0; $i < 7; $i++) {
            $DeliveredItemsAmounts[] = User_order::where('status', 'delivered')->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->sum('recieved_price');
        }

        for ($i=0; $i < 7; $i++) {
            $DeliversFeesSubWeek[] = User_order::where('status', 'delivered')->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->sum('Deliver_Fee');
        }
        
        // $IssuedOrders[] = 'Issued Orders (This Week)';
        // for ($i=0; $i < 7; $i++) {
        //     $IssuedOrders[] = User_order::where('status', 'issued')->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->count();
        // }

        // //Waiting Not Accepted 
        // $WaitingOrdersCountAccepted = Orders_group::where('accepted', 1)->select('id')->get()->map(function ($Group) {
        //     return User_order::where('OrderGroupe_Id', $Group->id)->where('status', 'waiting')->get()->count();
        // })->sum();
        // $WaitingOrdersAccepted[] = 'Waiting Orders Accepted (This Week)';
        // for ($i=0; $i < 7; $i++) {
        //     $WaitingOrdersAccepted[] = User_order::where(function ($query) { $query->Where('status', 'waiting')->whereNotNull('deliver_id'); })
        //         ->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->count();
        // }

        // //Waiting Accepted
        // $WaitingOrdersCountNotAccepted = Orders_group::where('accepted', 0)->select('id')->get()->map(function ($Group) {
        //     return User_order::where('OrderGroupe_Id', $Group->id)->where('status', 'waiting')->get()->count();
        // })->sum();
        // $WaitingOrdersNotAccepted[] = 'Waiting Orders Unaccepted (This Week)';
        // for ($i=0; $i < 7; $i++) {
        //     $WaitingOrdersNotAccepted[] = User_order::where(function ($query) { $query->Where('status', 'waiting')->whereNull('deliver_id'); })
        //         ->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->count();
        // }

        // //Pending
        // $PendingOrdersCount = User_order::where('status', 'pending')->get()->count();
        // $PendingOrders[] = 'Pending Orders (This Week)';
        // for ($i=0; $i < 7; $i++) {

        //     $PendingOrders[] = User_order::where(function ($query) { $query->Where('status', 'pending'); })
        //         ->whereBetween('created_at',[Carbon::now()->subDays($i+1), Carbon::now()->subDays($i)])->get()->count();
        // }

        // //Delivered
        // $DeliveredOrdersCount = User_order::where('status', 'delivered')->get()->count();
        // $DeliveredOrders[] = 'Delivered Orders (This Week)';
        // for ($i=0; $i < 7; $i++) {

        //     $DeliveredOrders[] = User_order::where('status', 'delivered')
        //         ->whereBetween('created_at',[Carbon::now()->subDays($i+1),Carbon::now()->subDays($i)])->get()->count();
        // } 

        return response()->json([
            "orders" => $orders,
            "GiftOrders" => $GiftOrders,
            "TaxiRides" => $TaxiRides,
            "users" => $users,
            "stores" => $stores,
            "delivers" => $delivers,
            "taxis" => $taxis,
            "Orders_black_list" => $Orders_black_list,
            "Deliver_Fee" => $Deliver_Fee,
            "Reports" => $Reports,
            "CountReports" => $CountReports,
            "WithdrawOrders" => $WithdrawOrders,
            "Withdraw_balance" => $Withdraw_balance,
            "UnWithdraw_balance" => $UnWithdraw_balance,
            "Users_Stores_balance" => $Users_Stores_balance,
            "recieved_prices" => $recieved_prices,

            "SendingStates" => $SendingStates,
            "ReceivingStates" => $ReceivingStates,
            "TopDelivers" => $TopDelivers,
            "TopStores" => $TopStores,
            "TopUsers" => $TopUsers, 

            "OrdersSubWeek" => $OrdersSubWeek, 
            "DeliveredItemsAmounts" => $DeliveredItemsAmounts, 
            "DeliversFeesSubWeek" => $DeliversFeesSubWeek, 
            

        ], 200);
    }

    // public function ShowAllTopDelivers()
    // {
    //     $TopDelivers = User_order::select('deliver_id')->distinct('deliver_id')->pluck('deliver_id')->all();
    //     foreach($TopDelivers as $DeliverId){
    //         $GetDeliver = Deliver::find($DeliverId);
    //         if($GetDeliver)
    //         {
    //             $RsTopDelivers[] = array(
    //                 "id" => $GetDeliver->id,
    //                 "Deliver" => $GetDeliver->first_name." ".$GetDeliver->last_name,
    //                 "Total_Orders" => User_order::where('deliver_id', $DeliverId)->get()->count()
    //             );
    //         }

    //     }
    //     array_multisort(array_column($RsTopDelivers, 'Total_Orders'), SORT_DESC, $RsTopDelivers);
    //     return response()->json($RsTopDelivers, 200);
    // }

    public function ShowAllTopMembers()
    {
        $TopMembers = User_order::select('user_id','account_type')->distinct('user_id')->get();
        foreach($TopMembers as $MemberId){
            $GetMember = table_byAccountType($MemberId->account_type,$MemberId->user_id); //Deliver::find($DeliverId);
            if($GetMember)
            {
                $RsTopMembers[] = array(
                    "id" => $GetMember->id,
                    "Member" => $GetMember->first_name." ".$GetMember->last_name,
                    "Total_Orders" => User_order::where('user_id', $GetMember->id)->get()->count(),
                    "account_type" => $MemberId->account_type
                );
            }

        }
        array_multisort(array_column($RsTopMembers, 'Total_Orders'), SORT_DESC, $RsTopMembers);
        return response()->json($RsTopMembers, 200);
    }
}
