<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'country_id',
        'name_ar',
        'name_en',
        'is_active',
        'integrate_id',
        'is_available_for_delivery',
        'polygon'
    ];


    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_available_for_delivery' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'countries');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_cities');
    }

    public function notDeliveryDateCity()
    {
        return $this->belongsTo(NotDeliveryDateCity::class);
    }
}
