<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subscriber_no' => $this->subscriber_no,
            'month' => $this->month,
            'year' => $this->year,
            'total_amount' => $this->total_amount,
            'phone_amount' => $this->phone_amount,
            'internet_amount' => $this->internet_amount,
            'paid_amount' => $this->paid_amount,
            'paid_status' => $this->is_paid ? 'Paid' : 'Unpaid',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}