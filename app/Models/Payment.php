<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_no',
        'month',
        'year',
        'amount',
        'status'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_no', 'subscriber_no');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class)
            ->where('subscriber_no', $this->subscriber_no)
            ->where('month', $this->month)
            ->where('year', $this->year);
    }
}