<?php

namespace App\Services\Parsers;

class PayTechParser {
    
    /**
     * Parses PayTech payload.
     * Format: Date, Amount (two decimals), "#", Reference, "#", Key-value pairs (Key / Value)
     * Example: 20250615156,50#202506159000001#note/debt payment march/internal_reference/A462JE81
     */
    public function parse(string $payload): array {
        $transactions = [];
        
        $lines = preg_split('/\r\n|\r|\n/', trim($payload));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('#', $line);
            if (count($parts) < 2) continue;
            
            $dateAndAmount = $parts[0];
            $reference = $parts[1];
            
            // Extract Date (first 8 chars: YYYYMMDD) and Amount (rest)
            if (strlen($dateAndAmount) <= 8) continue;
            
            $transactionDate = substr($dateAndAmount, 0, 8);
            $amountString = substr($dateAndAmount, 8);
            
            // Convert amount to integer (cents)
            $amountString = str_replace(',', '.', $amountString);
            $amount = (int) round(((float) $amountString) * 100);
            
            try {
                $formattedDate = \Carbon\Carbon::createFromFormat('Ymd', $transactionDate)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $formattedDate = $transactionDate;
            }
            
            $transactions[] = [
                'reference' => $reference,
                'amount' => $amount,
                'transaction_date' => $formattedDate,
                'metadata' => null,
                'notes' => null
            ];
        }
        
        return $transactions;
    }
}
