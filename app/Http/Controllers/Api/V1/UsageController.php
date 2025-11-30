<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsageResource;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Info(
 *     title="Mobile Billing API",
 *     version="1.0.0",
 *     description="Mobile Provider Billing System API",
 *     @OA\Contact(
 *         email="your-email@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Mobile Billing API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class UsageController extends Controller
{
    protected $usageService;

    public function __construct(UsageService $usageService)
    {
        $this->usageService = $usageService;
    }

/**
 * @OA\Post(
 *     path="/api/v1/usage",
 *     summary="Add a new usage record for a subscriber",
 *     tags={"Usage"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="subscriber_no", type="string", example="5551234567"),
 *             @OA\Property(property="month", type="integer", example=4),
 *             @OA\Property(property="usage_type", type="string", example="phone", enum={"phone", "internet"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usage added successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Usage added successfully")
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
    public function addUsage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscriber_no' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'usage_type' => 'required|string|in:phone,internet,Phone,Internet'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $usage = $this->usageService->addUsage(
                $request->subscriber_no,
                $request->usage_type,
                $request->month,
                $request->year ?? date('Y')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Usage added successfully',
                'data' => new UsageResource($usage)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}