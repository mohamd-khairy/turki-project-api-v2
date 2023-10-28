<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'product_ids',
        'discount_amount_percent',
        'min_applied_amount',
        'max_discount',
        'is_for_all',
        'is_percent',
        'is_active',
        'city_ids',
        'country_ids',
        'category_parent_ids',
        'category_child_ids',
        'expire_at',
        'use_times_per_user',
        'integrate_id',
        'is_by_city',
        'is_by_country',
        'is_by_category',
        'is_by_subcategory',
        'is_by_product',
        // new field
        'client_ids',
        'for_clients_only'
    ];

    
    protected $casts = [
        'expire_at' => 'datetime:Y-m-d H:m',
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
    ];
    
     public function scopeActive($query){
        return $query -> where('is_active',1) ;
    }

    public function setExpireAtAttribute($value) {
        $this->attributes['expire_at'] = (new Carbon($value))->format('Y-m-d H:m');
    }

      public static function isValid($coupon, $code, $productIds, $total)
    {
        if ($productIds == null || $productIds == [])
            return null;
            
        if ($coupon == null || !$coupon->is_active) {
            return null;
        }
        
        $expire_at = Carbon::make($coupon->expire_at)->timestamp;

        //if not valid reject
        if($expire_at < Carbon::now()->timestamp) {
            return null;
        }

        //if not valid reject
        if($coupon->min_applied_amount != null && $total < $coupon->min_applied_amount)
            return null;

        $usedTimes = Order::where([['customer_id', auth()->user()->id],['applied_discount_code', $code]])->get();

        if($usedTimes != [] && count($usedTimes) >= $coupon->use_times_per_user)
            return null;
            
        if($coupon->for_clients_only == true && $coupon->client_ids != null && empty($coupon->client_ids) == false){
            $clientId = auth()->user()->id;
            $validClientIds = explode(',', $coupon->client_ids);
            if (!in_array($clientId, $validClientIds)){
                return null;
            }
        }

        if ($coupon->is_for_all == false){

            $validCategoryIds = [];
            $validSubCategoryIds = [];
            $validProductIds = [];
            $validCityIds = [];
            $validCountryIds = [];
            
            if ($coupon->product_ids != null)
                $validProductIds = explode(',', $coupon->product_ids);

            if ($coupon->category_parent_ids != null)
                $validCategoryIds = explode(',', $coupon->category_parent_ids);

            if ($coupon->category_child_ids != null)
                $validSubCategoryIds = explode(',', $coupon->category_child_ids);
               
            if ($coupon->city_ids != null)
                $validCityIds = explode(',', $coupon->city_ids);
                
            if ($coupon->country_ids != null)
                $validCountryIds = explode(',', $coupon->country_ids);

            // products in cart
            foreach ($productIds as $productId){
                $product = Product::where('id',$productId)->active()->get()->first();
                $cities = $product->cities;
                
                $validCity = false;
                $validCountry = false;
                
                //if one not valid reject
                foreach($cities as $city){
                  
                    if (count($validCountryIds) != 0 && in_array($city->country_id, $validCountryIds)){
                        $validCountry = true;
                        break;
                    }
                    
                    if (count($validCityIds) != 0 && in_array($city->id, $validCityIds)){
                        // dd("invaild country", $city, $validCityIds);
                        $validCity = true;
                        break;
                    }
                }
                
                if(count($validCountryIds) != 0 && !$validCountry) {
                    return null;
                }
                //if one not valid reject
                if(count($validCityIds) != 0 && !$validCity) {
                    return null;
                }
                
                //if one not valid reject
                if (count($validProductIds) != 0 && !in_array($productId, $validProductIds)){
                    return null;
                }

                
                //if one not valid reject
                if (count($validCategoryIds) != 0 && !in_array($product->category_id, $validCategoryIds)){
                        return null;
                }

                //if one not valid reject
                if (count($validSubCategoryIds) != 0 && !in_array($product->sub_category_id, $validSubCategoryIds)){
                        return null;
                }
            }
        }

        return $coupon;
    }
    
      public static function isValidV2($coupon, $code, $productIds, $total, $countryId, $cityId)
    {
        if ($productIds == null || $productIds == [])
            return [1,"add items to cart"];

        if ($coupon == null || !$coupon->is_active) {
            return [2, "coupon is disabled"];
        }

        $expire_at = Carbon::make($coupon->expire_at)->timestamp;
        $currentTimestamp = Carbon::now()->timestamp;
        //if not valid reject
        if((int)$expire_at < (int)$currentTimestamp) {
            return [3, "coupon is expired"];
        }

        //if not valid reject (default 0)
        if($coupon->min_applied_amount > $total)
            return [4, "coupon not met minimum value ". $coupon->min_applied_amount];


        $usedTimes = Order::where([['customer_id', auth()->user()->id],['applied_discount_code', $code]])->get();

        if(count($usedTimes) >= $coupon->use_times_per_user)
            return [5, "coupon is used at maximum!"];

        if ($coupon->is_for_all){
            return [400, $coupon];
        }

        $entryCount = 0;

         //if not valid reject
        if($coupon->is_by_country){
            if ($coupon->country_ids != null && trim($coupon->country_ids) != '') {
                $validCountryIds = explode(',', trim($coupon->country_ids));
                if(!in_array($countryId, $validCountryIds)){
                    return [6, "coupon is not valid in this country"];
                }else{
                    $entryCount = $entryCount + 1;
                }
            }
        }


        //if not valid reject
        if($coupon->is_by_city){
            if ($coupon->city_ids != null && trim($coupon->city_ids) != '') {
                $validCityIds = explode(',', trim($coupon->city_ids));
                if(!in_array($cityId, $validCityIds)){
                    return [7, "coupon is not valid in this city"];
                }else{
                    $entryCount = $entryCount + 1;
                }
            }
        }

        $notApplicableProducts = [];
        // is applied for any products
        if (!$coupon->is_for_all){

            $validProductIds = [];
            if($coupon->is_by_product){
                if ($coupon->product_ids != null && trim($coupon->product_ids) != ''){
                    $validProductIds = explode(',', trim($coupon->product_ids));

                    // products in cart
                    foreach ($productIds as $productId){
                        if(!in_array($productId, $validProductIds)){
                            $notApplicableProducts[] = $productId;
//                            return [8, "coupon is not valid for some products"];
                        }else{
                            $entryCount = $entryCount + 1;
                        }
                    }
                }
            }

            $validCategoryIds = [];
            if($coupon->is_by_category){
                if ($coupon->category_parent_ids != null && trim($coupon->category_parent_ids) != ''){
                    $validCategoryIds = explode(',', trim($coupon->category_parent_ids));

                    // products in cart
                    foreach ($productIds as $productId){
                        $product = Product::where('id',$productId)->active()->get()->first();
                        if(!in_array($product->category_id, $validCategoryIds)){
                            $notApplicableProducts[] = $productId;
//                            return [9, "coupon is not valid for some category"];
                        }else{
                            $entryCount = $entryCount + 1;
                        }
                    }

                }
            }


            $validSubCategoryIds = [];
            if($coupon->is_by_subcategory){
                if ($coupon->category_child_ids != null && trim($coupon->category_child_ids) != ''){
                    $validSubCategoryIds = explode(',', trim($coupon->category_child_ids));

                    // products in cart
                    foreach ($productIds as $productId){
                        $product = Product::where('id',$productId)->active()->get()->first();
                        if($product->sub_category_id != null){
                            if(!in_array($product->sub_category_id, $validSubCategoryIds)){
                                $notApplicableProducts[] = $productId;
//                                return [10, "coupon is not valid for some subcategory"];
                            }else{
                                $entryCount = $entryCount + 1;
                            }
                        }
                    }
                }
            }
        }
        
        
        if($coupon->for_clients_only){
            if ($coupon->client_ids != null && trim($coupon->client_ids) != ''){
                $clientId = auth()->user()->id;
                $validClientIds = explode(',', trim($coupon->client_ids));
                if (!in_array($clientId, $validClientIds)){
                    return [11, "coupon is not valid for this client"];
                }else{
                    $entryCount = $entryCount + 1;
                }
            }
        }
        

        if ($entryCount > 0 && count($notApplicableProducts) != 0)
            return [401, $coupon, $notApplicableProducts];
        else if ($entryCount > 0)
            return [400, $coupon];
        else
            return [500, "coupon is not valid"];
    }
    
    
    public function product(){
       return $this->belongsToMany(Product::class);
    }

    public function category(){
      return  $this->belongsToMany(Category::class);
    }

    public function cart(){
        return  $this->belongsTo(Cart::class);
    }
    
     public function discountCities()
    {
        return $this->belongsToMany(City::class, 'discount_cities');
    }

}
