<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_no',
        'month',
        'year',
        'total_amount',
        'phone_amount',
        'internet_amount',
        'paid_amount',
        'is_paid'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_no', 'subscriber_no');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscriber_no', 'subscriber_no')
            ->where('month', $this->month)
            ->where('year', $this->year);
    }
}