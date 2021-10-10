<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Store_item_rate;

class Store_item extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;


        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

        // protected $attributes = ['rate'];

        protected $appends = ['rate'];


        protected $fillable = [
             'store_id', 'item', 'price', 'sizes' ,'colors', 'image', 'description', 'quantity', 'available', 'branch_ids', 
            //  'rate',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'branch_ids',
            'available',
            'updated_at',
            'created_at',
            'store_id',
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        public function getJWTCustomClaims()
        {
            return [];
        }

        public function getSizesAttribute()
        {
            return json_decode($this->attributes['sizes']);
        }

        public function getColorsAttribute()
        {
            return json_decode($this->attributes['colors']);
        }

        public function getRateAttribute()
        {
            $rate = Store_item_rate::where('item_id', $this->id)->get();
            return ($rate->count() == 0) ? 1 : floor($rate->sum('rate') / $rate->count());
        }

         
    }
