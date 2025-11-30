<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;

class PaymentService
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Pay a bill (supports partial payments).
     */
    public function payBill(string $subscriberNo, int $month, int $year, float $amount)
    {
        // Get or calculate bill
        $bill = $this->billingService->getBill($subscriberNo, $month, $year);

        $remainingAmount = $bill->total_amount - $bill->paid_amount;

        if ($remainingAmount <= 0) {
            return [
                'status'  => 'Error',
                'message' => 'Bill is already fully paid',
            ];
        }

        if ($amount <= 0) {
            return [
                'status'  => 'Error',
                'message' => 'Payment amount must be greater than zero',
            ];
        }

        $paidNow = min($amount, $remainingAmount);

        $payment = Payment::create([
            'subscriber_no' => $subscriberNo,
            'month'         => $month,
            'year'          => $year,
            'amount'        => $paidNow,
            'status'        => 'Successful',
        ]);

        $bill->paid_amount += $paidNow;
        $bill->is_paid      = $bill->paid_amount >= $bill->total_amount;
        $bill->save();

        return [
            'status'  => 'Successful',
            'payment' => $payment,
            'bill'    => $bill,
        ];
    }
}
