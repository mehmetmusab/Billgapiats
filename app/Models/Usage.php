<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_no',
        'type',
        'amount',
        'month',
        'year'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_no', 'subscriber_no');
    }
}