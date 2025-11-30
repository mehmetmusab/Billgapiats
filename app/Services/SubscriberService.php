<?php

namespace App\Services;

use App\Models\Subscriber;

class SubscriberService
{
    public function getBySubscriberNo(string $subscriberNo)
    {
        return Subscriber::where('subscriber_no', $subscriberNo)->first();
    }

    public function createIfNotExists(string $subscriberNo)
    {
        $subscriber = $this->getBySubscriberNo($subscriberNo);
        
        if (!$subscriber) {
            $subscriber = Subscriber::create([
                'subscriber_no' => $subscriberNo
            ]);
        }
        
        return $subscriber;
    }
}