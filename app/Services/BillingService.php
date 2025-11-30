<?php

namespace App\Services;

use App\Models\Bill;
use Carbon\Carbon;

class BillingService
{
    protected $usageService;
    protected $subscriberService;

    public function __construct(UsageService $usageService, SubscriberService $subscriberService)
    {
        $this->usageService = $usageService;
        $this->subscriberService = $subscriberService;
    }

    public function calculateBill(string $subscriberNo, int $month, int $year)
    {
        // Abone varsa kullan, yoksa oluştur
        $this->subscriberService->createIfNotExists($subscriberNo);
        
        // Önce bu abone için bu ay içindeki kullanımı al
        $usage = $this->usageService->getMonthlyUsage($subscriberNo, $month, $year);
        
        // Telefon ücretini hesapla - 1000 dakika ücretsiz, sonraki her 1000 dakika 10$
        $phoneAmount = 0;
        $phoneUsage = $usage['phone'];
        if ($phoneUsage > 1000) {
            $phoneAmount = ceil(($phoneUsage - 1000) / 1000) * 10;
        }
        
        // İnternet ücretini hesapla - 20GB'a kadar 50$, sonraki her 10GB için 10$
        $internetAmount = 0;
        $internetUsage = $usage['internet']; // MB cinsinden
        $internetGb = $internetUsage / 1024; // GB'a çevir
        
        if ($internetGb > 0) {
            $internetAmount = 50; // Baz ücret (20GB'a kadar)
            
            if ($internetGb > 20) {
                $internetAmount += ceil(($internetGb - 20) / 10) * 10;
            }
        }
        
        $totalAmount = $phoneAmount + $internetAmount;
        
        // Mevcut faturayı güncelle veya yeni fatura oluştur
        $bill = Bill::updateOrCreate(
            [
                'subscriber_no' => $subscriberNo,
                'month' => $month,
                'year' => $year
            ],
            [
                'total_amount' => $totalAmount,
                'phone_amount' => $phoneAmount,
                'internet_amount' => $internetAmount
            ]
        );
        
        return $bill;
    }

    public function getBill(string $subscriberNo, int $month, int $year)
    {
        $bill = Bill::where('subscriber_no', $subscriberNo)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
            
        if (!$bill) {
            // Fatura yoksa hesapla
            $bill = $this->calculateBill($subscriberNo, $month, $year);
        }
        
        return $bill;
    }

    public function getBillsPaginated(string $subscriberNo, int $month, int $year, int $perPage = 10)
    {
        return Bill::where('subscriber_no', $subscriberNo)
            ->where('month', $month)
            ->where('year', $year)
            ->paginate($perPage);
    }


    /**
     * Admin - Add Bill for a given subscriber and month/year.
     */
    public function addBill(string $subscriberNo, int $month, int $year)
    {
        // Reuse existing bill calculation logic
        return $this->calculateBill($subscriberNo, $month, $year);
    }

    /**
     * Admin - Add bills from CSV.
     * CSV columns: subscriber_no,month,year
     */
    public function addBillsFromCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('CSV file cannot be opened.');
        }

        $results = [];
        $header  = fgetcsv($handle); // assume first row is header

        while (($row = fgetcsv($handle)) !== false) {
            $subscriberNo = $row[0] ?? null;
            $month        = isset($row[1]) ? (int) $row[1] : null;
            $year         = isset($row[2]) ? (int) $row[2] : now()->year;

            if (!$subscriberNo || !$month) {
                $results[] = [
                    'subscriber_no' => $subscriberNo,
                    'month'         => $month,
                    'year'          => $year,
                    'status'        => 'Error',
                    'message'       => 'Missing subscriber_no or month',
                ];
                continue;
            }

            try {
                $bill = $this->calculateBill($subscriberNo, $month, $year);

                $results[] = [
                    'subscriber_no' => $subscriberNo,
                    'month'         => $month,
                    'year'          => $year,
                    'status'        => 'Success',
                    'bill_id'       => $bill->id,
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'subscriber_no' => $subscriberNo,
                    'month'         => $month,
                    'year'          => $year,
                    'status'        => 'Error',
                    'message'       => $e->getMessage(),
                ];
            }
        }

        fclose($handle);

        return $results;
    }

    /**
     * Banking App - get unpaid bills for subscriber.
     */
    public function getUnpaidBills(string $subscriberNo)
    {
        return Bill::where('subscriber_no', $subscriberNo)
            ->where('is_paid', false)
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

}