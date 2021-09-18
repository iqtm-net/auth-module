<?php

namespace App\Http\Controllers\api;

use App\User_order;
use App\Deliver;
use App\prod_price;
use App\Prod_price_local;
use App\Orders_group;
use App\Orders_black_list;
use App\Event;
use App\Offers_branch;
use App\Offers_order;
use App\Offers_orders_cart;
use App\Offer;
use App\Offer_rate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BooksExport;
use App\Events\RTNotify;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTAuth;
use Session;
use Illuminate\Support\Facades\Storage;
//

class Barcode extends Controller
{
     

    public function post()
    {

    }

}
