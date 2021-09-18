<?php

namespace App\Models\HodHod_Taxi;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Taxi_trip extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'client_id',
            'client_role',
            'taxi_id',
            'taxi_type',
            'start_location',
            'start_state',
            'end_location',
            'end_state',
            'distance',
            'basic_price',
            'final_price',
            'expected_arrival_minutes',
            'status',
            'canceled_by',
            'cancelation_reason',
            'Code',
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

        public function Taxi_details()
        {
            return $this->hasOne('App\Taxi', 'id', 'taxi_id')
            ->select(
                'first_name as taxi_first_name',
                'last_name as taxi_last_name',
                'phone_number as taxi_phone_number'
            );
        }
        
        public function User_details()
        {
            return $this->hasOne('App\User', 'id', 'client_id')
            ->select(
                'first_name as client_first_name',
                'last_name as client_last_name',
                'phone_number as client_phone_number'
            );
        
        
        }public function Store_details()
        {
            return $this->hasOne('App\Store', 'id', 'client_id')
            ->select(
                'first_name as client_first_name',
                'last_name as client_last_name',
                'phone_number as client_phone_number'
            );
        }

    }
