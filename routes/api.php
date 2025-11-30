<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BillingController;
use App\Http\Controllers\Api\V1\UsageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'Pong!',
        'time'    => now(),
    ]);
});

Route::prefix('v1')->group(function () {

    /*
     * MOBILE PROVIDER APP
     * -------------------
     * Query Bill  -> Auth: YES, Paging: NO, Rate limit: 3 call / subscriber / day
     * Query Bill Detailed -> Auth: YES, Paging: YES
     */

    // Query Bill (Mobile App) - AUTH + subscriber bazlı limit
    Route::middleware(['auth:api', 'subscriber.query.limit'])
        ->get('/bill', [BillingController::class, 'queryBill']);

    // Query Bill Detailed (Mobile App) - AUTH + pagination
    Route::middleware('auth:api')
        ->get('/bill-detailed', [BillingController::class, 'queryBillDetailed']);

    /*
     * BANKING APP
     * -----------
     * Query Bill -> AUTH: YES, sadece ödenmemiş faturalar (unpaid)
     */
    Route::middleware('auth:api')
        ->get('/bank/bills/unpaid', [BillingController::class, 'queryUnpaidBills']);

    /*
     * WEB SITE
     * --------
     * Pay Bill -> AUTH: NO
     */
    Route::post('/pay-bill', [BillingController::class, 'payBill']);

    /*
     * ADMIN
     * -----
     * Add Bill -> AUTH: YES
     * Add Bill Batch (.csv) -> AUTH: YES
     */
    Route::middleware('auth:api')->group(function () {
        Route::post('/admin/bill', [BillingController::class, 'addBill']);
        Route::post('/admin/bill-batch', [BillingController::class, 'addBillBatch']);
    });

    // Usage endpoint (existing)
    Route::post('/usage', [UsageController::class, 'addUsage']);

    // Auth routes (JWT)
    Route::post('/auth/login',   [AuthController::class, 'login']);
    Route::post('/auth/logout',  [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
});
