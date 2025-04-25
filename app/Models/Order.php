<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id'; // Important because we set 'order_id' manually
    public $timestamps = false; // We use custom timestamps (date_added, date_modified)

    protected $fillable = [
        'user_id',
        'address_id',
        'subtotal',
        'shipping',
        'total',
        'payment_code',
        'logistic',
        'track',
        'order_status',
        'date_added',
        'date_modified',
    ];

    // User who placed the order
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Address for delivery
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Products inside this order
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id');
    }
}
