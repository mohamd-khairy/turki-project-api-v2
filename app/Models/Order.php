<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{



    use HasFactory,SoftDeletes;
    protected $fillable = [
        'id',
        'ref_no',
        'delivery_fee',
        'order_subtotal',
        'total_amount',
        'total_amount_after_discount',
        "comment",
        "using_wallet",
        'wallet_amount_used',
        "address",
        'customer_id',
        'order_state_id',
        'payment_type_id',
        'applied_discount_code',
        'address_id',
        'delivery_date',
        'shalwata_id',
        'delivery_period_id',
        'payment_id',
        'integrate_id',
        'saleOrderId',
        'version_app'
    ];

    protected $hidden = ['id', 'address'];
    protected $primaryKey = 'ref_no';
    public $incrementing = false;

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function orderState(){
        return $this->belongsTo(OrderState::class, 'order_state_id');
    }
    
    public function selectedAddress(){
        return $this->belongsTo(Address::class, 'address_id')->select(['id','address', 'comment', 'label', 'long','lat']);
    }

    public function deliveryPeriod(){
        return $this->belongsTo(DeliveryPeriod::class, 'delivery_period_id');
    }

      public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_ref_no', 'product_id')->with('productImages');
    }
    
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class,'order_ref_no')->with('preparation', 'size', 'cut', 'shalwata', 'product.productImages');
    }
    

}
