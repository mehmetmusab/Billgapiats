<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubscriberQueryLimit
{
    /**
     * 3 calls per subscriber per day for Query Bill endpoint.
     */
    public function handle(Request $request, Closure $next)
    {
        $subscriberNo = $request->input('subscriber_no');

        if (!$subscriberNo) {
            return response()->json([
                'status'  => 'error',
                'message' => 'subscriber_no is required for rate limiting',
            ], 400);
        }

        $today = now()->toDateString();
        $key   = "subscriber:{$subscriberNo}:query-bill:{$today}";

        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->endOfDay());

        if ($count > 3) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Daily query limit exceeded for this subscriber (max 3 per day).',
            ], 429);
        }

        return $next($request);
    }
}
