<?php

namespace App\Services\Parsers;

class AcmeParser {
    
    /**
     * Parses Acme Bank payload.
     * Format: Amount (two decimals), "//", Reference, "//", Date
     * Example: 156,50//202506159000001//20250615
     */
    public function parse(string $payload): array {
        $transactions = [];
        
        $lines = preg_split('/\r\n|\r|\n/', trim($payload));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('//', $line);
            if (count($parts) < 3) continue;
            
            $amountString = $parts[0];
            $reference = $parts[1];
            $transactionDate = $parts[2];
            
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
