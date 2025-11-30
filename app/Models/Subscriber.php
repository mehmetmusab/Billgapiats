<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = ['subscriber_no', 'name'];

    public function usages()
    {
        return $this->hasMany(Usage::class, 'subscriber_no', 'subscriber_no');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'subscriber_no', 'subscriber_no');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscriber_no', 'subscriber_no');
    }
}