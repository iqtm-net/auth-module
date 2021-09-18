<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Offers_order extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;


        /**
         * The attributes that are mass assignable.
         *
         * //ALTER TABLE `offers_orders` ADD `hide_sender_infos` BOOLEAN NOT NULL AFTER `account_type`, ADD `receiver_full_name` VARCHAR(250) NOT NULL AFTER `hide_sender_infos`, ADD `receiver_phone_number` BIGINT NOT NULL AFTER `receiver_full_name`, ADD `receiver_address` VARCHAR(250) NOT NULL AFTER `receiver_phone_number`, ADD `receiver_location` VARCHAR(250) NOT NULL AFTER `receiver_address`, ADD `receiver_gender` VARCHAR(250) NOT NULL AFTER `receiver_location`, ADD `card_text` VARCHAR(1000) NOT NULL AFTER `receiver_gender`;

         * @var array
         */
        protected $fillable = [
            'offer_id',
            'account_id',
            'account_type',
            'hide_sender_infos',
            'receiver_full_name',
            'receiver_phone_number',
            'receiver_address',
            'receiver_location',
            'receiver_gender',
            'card_text',
            'notes',
            'status',
            'total_price',
            'order_number',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        public function getJWTCustomClaims()
        {
            return [];
        }

        public function SubBranches()
        {
            return $this->hasMany('App\Offer', 'brache_id');
        }
    }
