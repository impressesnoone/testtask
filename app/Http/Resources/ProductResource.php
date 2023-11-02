<?php

namespace App\Http\Resources;

use App\Models\CartSave;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cart_save_products_count = CartSave::select('count')->where('user_id', auth()->user()->id)->get();
        return [
            'count' => $cart_save_products_count,
            'title' => $this->title,
            'description' => $this->description,
            'cost' => $this->cost,
            'width' => $this->width,
            'height' => $this->height,
            'weight' => $this->weight,
        ];
    }
}
