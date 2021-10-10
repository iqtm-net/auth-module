<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Store_item;
use App\Store;

class Customer_cart extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

        // protected $appends = ['store_id'];

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'ip',
            'item_id',
            'size',
            'color',
            'quantity',
            'in_cart',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            // 'id',
            'ip',
            'updated_at',
            'created_at',
            'item_id',
            'in_cart',
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        
        public function getJWTCustomClaims()
        {
            return [];
        }

        public function item()
        {
            return $this->hasOne(Store_item::class, 'id', 'item_id');
        }

        // public function store()
        // {
        //     return $this->hasOne(Store::class, 'id', $this->item->store_id);
        // }
        
        // public static function boot()
        // {
        //     parent::boot();

        //     static::creating(function($model)
        //     {
        //         $model->ip = '111';
        //         // $model->foo = $model->foo . '-' . $model->id;
        //         $model->save();
        //     });
        // }

        // public function setAttribute($key, $value) {
            
        //     $key['ip'] = 'sas';
        //     // $this->attributes['ip'] = $key;
        //     // $this->attributes['item_id'] = $value;
        //     // $this->attributes['size'] = 'sasas';
        
        // }
    }
