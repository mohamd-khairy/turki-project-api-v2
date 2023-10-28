<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
     protected $fillable = ['ref_no','customer_id', 'payment_type_id', 'order_ref_no', 'bank_ref_no', 'price', 'description', 'status', 'manual'];
    
}
