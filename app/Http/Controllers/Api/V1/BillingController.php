<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillDetailResource;
use App\Http\Resources\BillResource;
use App\Services\BillingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    protected $billingService;
    protected $paymentService;

    public function __construct(BillingService $billingService, PaymentService $paymentService)
    {
        $this->billingService = $billingService;
        $this->paymentService = $paymentService;
    }

/**
 * @OA\Post(
 *     path="/api/v1/calculate-bill",
 *     summary="Calculate bill for a subscriber",
 *     tags={"Billing"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="subscriber_no", type="string", example="5551234567"),
 *             @OA\Property(property="month", type="integer", example=4),
 *             @OA\Property(property="year", type="integer", example=2025)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Bill calculated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Bill calculated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
    public function calculateBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bill = $this->billingService->calculateBill(
                $request->subscriber_no,
                $request->month,
                $request->year
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Bill calculated successfully',
                'data' => new BillResource($bill)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

/**
 * @OA\Get(
 *     path="/api/v1/bill",
 *     summary="Query bill for a subscriber",
 *     tags={"Billing"},
 *     @OA\Parameter(
 *         name="subscriber_no",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="month",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Bill retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Bill retrieved successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
    public function queryBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bill = $this->billingService->getBill(
                $request->subscriber_no,
                $request->month,
                $request->year
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Bill retrieved successfully',
                'data' => new BillResource($bill)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

/**
 * @OA\Get(
 *     path="/api/v1/bill-detailed",
 *     summary="Query bill with details for a subscriber",
 *     tags={"Billing"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="subscriber_no",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="month",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detailed bill retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
    public function queryBillDetailed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $perPage = $request->per_page ?? 10;
            $bills = $this->billingService->getBillsPaginated(
                $request->subscriber_no,
                $request->month,
                $request->year,
                $perPage
            );

            return BillDetailResource::collection($bills)
                ->additional([
                    'status' => 'success',
                    'message' => 'Detailed bill retrieved successfully'
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve detailed bill',
                'error' => $e->getMessage()
            ], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/api/v1/pay-bill",
 *     summary="Pay bill for a subscriber",
 *     tags={"Billing"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="subscriber_no", type="string", example="5551234567"),
 *             @OA\Property(property="month", type="integer", example=4),
 *             @OA\Property(property="year", type="integer", example=2025)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Bill paid successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Bill paid successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */
    public function payBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer',
            'amount'        => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->paymentService->payBill(
                $request->subscriber_no,
                $request->month,
                $request->year,
                $request->amount
            );

            if ($result['status'] === 'Error') {
                return response()->json([
                    'status'  => 'error',
                    'message' => $result['message'],
                ], 400);
            }

            return response()->json([
                'status'  => 'success',
                'message' => $result['bill']->is_paid
                    ? 'Bill paid successfully'
                    : 'Partial payment recorded, remaining amount is saved',
                'data'    => new BillResource($result['bill']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to pay bill',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Admin - Add Bill
     */
    public function addBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $year = $request->input('year', now()->year);

        try {
            $bill = $this->billingService->addBill(
                $request->subscriber_no,
                $request->month,
                $year
            );

            return (new BillDetailResource($bill))->additional([
                'status'  => 'success',
                'message' => 'Bill created/updated successfully by admin',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to add bill',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin - Add Bill Batch (CSV)
     */
    public function addBillBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $file    = $request->file('file');
            $results = $this->billingService->addBillsFromCsv($file->getRealPath());

            return response()->json([
                'status'  => 'success',
                'message' => 'Batch bill import completed',
                'data'    => $results,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to import batch bills',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Banking App - Unpaid bills
     */
    public function queryUnpaidBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $bills = $this->billingService->getUnpaidBills($request->subscriber_no);

            return BillResource::collection($bills)->additional([
                'status'  => 'success',
                'message' => 'Unpaid bills retrieved successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to retrieve unpaid bills',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}