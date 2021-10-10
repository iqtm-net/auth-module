<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Store extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable; 

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'first_name', 'last_name', 'address_country', 'address_state', 'address_region', 'more_address_details', 'phone_number', 'email', 'password', 'email_verified_at', 'store_name', 'balance','id', 'Code',
            'delivery_type', 
            'global_deliver_fee',
            'local_deliver_fee',
            'confirmed', 'specialties', 'store_type', 'store_theme_id', 'subdomain_name', 'theme_logo'
        ];
        
        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            //'password', 
            'remember_token','password'
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        public function getJWTCustomClaims()
        {
            return [];
        }
    }