<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User_order extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable; 

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'Deliver_Fee', 'deliver_track_code', 'color', 'quantity', 'product_id', 'product_image', 'Order_Discount', 'handeled_by', 'delayed', 'App_Fee', 'size', 'insurance', 'location_from_country', 'location_from_state', 'location_from_region', 'deliver_id', 'user_id', 'sender_full_name', 'sender_phone_number', 'account_type', 'receiver_full_name', 'reciever_phone_number', 'recieved_price', 'location_to_country', 'location_to_state', 'location_to_region', 'recieve_date', 'shipping_type', 'location_on_map_to', 'distance','product_name','id', 'in_cart', 'OrderGroupe_Id', 'location_on_map_from', 'track_code',  'case_details', 'status', 'created_by_shared_link', 'payment_method',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */

        public function Cart()
        {
            return $this->hasOne('App\Orders_group','id', 'OrderGroupe_Id');
        }

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        public function getJWTCustomClaims()
        {
            return [];
        }
    }