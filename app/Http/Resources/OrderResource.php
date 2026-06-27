<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'shop' => [
                'name' => $this->shop->name ?? translate('Shop not found'),
                'slug' => $this->shop->slug ?? '',
            ],
            'payment_type' => $this->payment_type,
            'manual_payment' => $this->manual_payment,
            'manual_payment_data' => $this->manual_payment_data ? json_decode($this->manual_payment_data) : null,
            'delivery_type' => $this->delivery_type,
            'delivery_status' => $this->delivery_status,
            'metodo_pago_contraentrega' => $this->metodo_pago_contraentrega,
            'payment_status' => $this->payment_status,
            'coupon_discount' => (float) $this->coupon_discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'grand_total' => (float) $this->grand_total,
            'subtotal' => (float) $this->orderDetails->sum('total') - $this->calculateTotalTax($this->orderDetails),
            'tax' => (float) $this->calculateTotalTax($this->orderDetails),
            'products' => new OrderProductCollection($this->orderDetails),
            'collections' => $this->collectionDetails,
            'created_at' => strtotime($this->created_at),
            'has_refund_request' => $this->refundRequests->count() > 0 ? true : false,
            'courier_name' => $this->courier_name,
            'tracking_number' => $this->tracking_number,
            'tracking_url' => $this->tracking_url,
            'order_updates' => $this->order_updates
        ];
    }

    protected function calculateTotalTax($orderDetails)
    {
        $tax = 0;

        foreach ($orderDetails as $item) {
            $tax += $item->tax * $item->quantity;
        }

        return $tax;
    }
}
