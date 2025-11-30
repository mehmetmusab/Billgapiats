<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subscriber_no' => $this->subscriber_no,
            'type' => $this->type,
            'amount' => $this->amount,
            'month' => $this->month,
            'year' => $this->year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}