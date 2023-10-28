<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'title_color',
        'sub_title',
        'sub_title_color',
        'button_text',
        'button_text_color',
        'redirect_url',
        'is_active',
        'type',
        'redirect_mobile_url',
        'product_id',
        'category_id'
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(){
        return config('app.url').Storage::url('app/public/marketingBoxImages/'.$this->id.'/'.$this->image);
    }

    public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function bannerCities()
    {
        return $this->belongsToMany(City::class, 'banner_cities');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
