<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User_order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return "asdasd";//view('home');
    }

    public function test2()
    {
        $get = User_order::select('recieve_date', 'track_code','Deliver_Fee','recieved_price')->where('deliver_id', 386)->where('status', 'delivered')->orderBy('created_at','asc')->get();
        $total = $get->sum('Deliver_Fee') + $get->sum('recieved_price');
        $total2 = $get->sum('Deliver_Fee');
        foreach($get as $val){
            $val['Credit_Invoice'] = $val->Deliver_Fee + $val->recieved_price;
            $get_orders[] = $val;
        }

        return view('Stat')->with([
            'get_orders' => $get_orders,
            'total' => $total,
            'Benifit' => $total2,
        ]);
    }

    public function test(Request $request)
    {
        $get = User_order::select('recieve_date', 'track_code','Deliver_Fee','recieved_price')->where('deliver_id', 386)->where('status', $request->input('status'))->orderBy('created_at','asc')->get();
        $total = $get->sum('Deliver_Fee') + $get->sum('recieved_price');
        $total2 = $get->sum('Deliver_Fee');

        foreach($get as $val){
            $val['Credit_Invoice'] = $val->Deliver_Fee + $val->recieved_price;
            $get_orders[] = $val;
        }

        return view('Stat')->with([
            'get_orders' => $get_orders,
            'total' => $total,
            'Benifit' => $total2,
        ]);
    }

}
