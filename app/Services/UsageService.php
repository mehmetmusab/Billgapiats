<?php

namespace App\Services;

use App\Models\Usage;
use Carbon\Carbon;

class UsageService
{
    protected $subscriberService;

    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    public function addUsage(string $subscriberNo, string $type, int $month, int $year = null)
    {
        // Abone varsa kullan, yoksa oluştur
        $this->subscriberService->createIfNotExists($subscriberNo);
        
        // Yıl belirtilmemişse şu anki yılı kullan
        if (!$year) {
            $year = Carbon::now()->year;
        }
        
        // Kullanım tipi kontrolü
        if (!in_array(strtolower($type), ['phone', 'internet'])) {
            throw new \InvalidArgumentException("Usage type must be 'phone' or 'internet'");
        }
        
        // Kullanım miktarını belirleme (telefon için 10 dakika, internet için 1 MB)
        $amount = strtolower($type) === 'phone' ? 10 : 1;
        
        // Kullanım kaydı oluştur
        $usage = Usage::create([
            'subscriber_no' => $subscriberNo,
            'type' => strtolower($type),
            'amount' => $amount,
            'month' => $month,
            'year' => $year
        ]);
        
        return $usage;
    }

    public function getMonthlyUsage(string $subscriberNo, int $month, int $year)
    {
        $phoneUsage = Usage::where('subscriber_no', $subscriberNo)
            ->where('month', $month)
            ->where('year', $year)
            ->where('type', 'phone')
            ->sum('amount');
            
        $internetUsage = Usage::where('subscriber_no', $subscriberNo)
            ->where('month', $month)
            ->where('year', $year)
            ->where('type', 'internet')
            ->sum('amount');
            
        return [
            'phone' => $phoneUsage,
            'internet' => $internetUsage
        ];
    }
}