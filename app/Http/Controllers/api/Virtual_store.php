<?php

    namespace App\Http\Controllers\api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;

    class Virtual_store extends Controller
    {   

        public function view_item($item_id){
            $get = Stores_theme::orderBy('id','DESC')
            ->where('active', 1)
            ->get()
            ->map(function ($item) {
                $item['image'] = url().'/images/stores_themes/'.$item->image;
                return $item;
            });

            return ResultNoSB($get, 200);
        }

    }
