<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'customer_id','country_iso_code','country_id','city_id','address','comment','label','is_default','long','lat',
    ];

    protected $hidden = ['created_at', 'updated_at'];
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function city(){
        return $this->belongsTo(City::class, 'city_id')->select(['name_en','name_ar']);
    }
}
