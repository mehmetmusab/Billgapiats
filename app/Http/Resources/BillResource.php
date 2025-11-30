<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subscriber_no' => $this->subscriber_no,
            'month' => $this->month,
            'year' => $this->year,
            'total_amount' => $this->total_amount,
            'remaining_amount' => $this->total_amount - ($this->paid_amount ?? 0),
            'paid_status' => $this->is_paid ? 'Paid' : 'Unpaid',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}