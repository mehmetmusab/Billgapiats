<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class SubscribersTableSeeder extends Seeder
{
    public function run()
    {
        $subscribers = [
            ['subscriber_no' => '5551234567', 'name' => 'John Doe'],
            ['subscriber_no' => '5559876543', 'name' => 'Jane Smith'],
            ['subscriber_no' => '5551112222', 'name' => 'Bob Johnson']
        ];

        foreach ($subscribers as $subscriber) {
            Subscriber::create($subscriber);
        }
    }
}