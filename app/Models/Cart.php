<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'email',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_products')->withPivot('count');
    }
}
