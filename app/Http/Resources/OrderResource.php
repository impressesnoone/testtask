<?php

namespace App\Http\Resources;

use App\Models\CartSave;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => Order::find($this->order_id)->status,
            'products' => [
                'product' => Product::where('id', $this->product_id)->get(),
                'count' => $this->count
            ],
        ];
    }
}
