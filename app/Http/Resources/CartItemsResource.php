<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product_name' => $this->product->title,
            'unit_price' => $this->product->final_price ?: $this->product->main_price,
            'total_item_price' => ($this->product->final_price ?: $this->product->main_price) * $this->quantity,
        ];
    }
}
