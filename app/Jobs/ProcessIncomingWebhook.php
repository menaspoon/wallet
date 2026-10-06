<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Webhooks;
use App\Models\Transactions;
use App\Services\Parsers\PayTechParser;
use App\Services\Parsers\AcmeParser;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ProcessIncomingWebhook implements ShouldQueue {

    use Queueable;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];
    
    public function __construct(public Webhooks $webhook) {}

    public function handle(PayTechParser $payTechParser, AcmeParser $acmeParser): void {

        $webhook = $this->webhook->fresh();

        if (!$webhook) {
            throw new RuntimeException('Webhook record not found.');
        }

        if ($webhook->status === 'processed') {
            return;
        }

        $webhook->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        $parser = match ($webhook->bank) {
            'paytech' => $payTechParser,
            'acme' => $acmeParser,
            default => throw new RuntimeException("البنك غير مدعوم: {$webhook->bank}"),
        };

        // Parse the raw webhook payload (can contain multiple transactions)
        $transactionsData = $parser->parse($webhook->payload);

        DB::transaction(function () use ($webhook, $transactionsData) {
            foreach ($transactionsData as $data) {
                // Verify required fields
                foreach (['reference', 'amount', 'transaction_date'] as $field) {
                    if (!array_key_exists($field, $data)) {
                        throw new RuntimeException(
                            "Missing parsed field: {$field}"
                        );
                    }
                }

                // Insert or update transaction to maintain idempotency
                Transactions::updateOrCreate(
                    [
                        'clint_id' => $webhook->clint_id,
                        'reference' => $data['reference'],
                    ],
                    [
                        'webhook_id' => $webhook->id,
                        'amount' => $data['amount'],
                        'transaction_date' => $data['transaction_date'],
                        'metadata' => $data['metadata'] ?? null,
                    ]
                );
            }

            $webhookUpdateData = [
                'status' => 'processed',
                'updated_at' => now(), // there is no processed_at column in migration usually, using updated_at implicitly
                'error_message' => null,
            ];

            if (!empty($transactionsData)) {
                $firstData = $transactionsData[0];
                $webhookUpdateData['reference'] = $firstData['reference'] ?? null;
                $webhookUpdateData['amount'] = $firstData['amount'] ?? null;
                $webhookUpdateData['transaction_date'] = $firstData['transaction_date'] ?? null;
                $webhookUpdateData['metadata'] = $firstData['metadata'] ?? null;
                $webhookUpdateData['notes'] = $firstData['notes'] ?? null;
            }

            $webhook->update($webhookUpdateData);
        });
    }

    public function failed(?Throwable $exception): void
    {
        $this->webhook->newQuery()
            ->whereKey($this->webhook->getKey())
            ->update([
                'status' => 'failed',
                'error_message' => $exception?->getMessage(),
            ]);
    }
}














// ظهرت مشكلة أثناء التنفيذ ولا يصح إكمال العملية أو حفظ بيانات ناقصة.
// throw new RuntimeException('الرسالة');


// match تستخدم لمقارنة النتائج او مثل دالة if 
// $المتغير_الذي_يخزن_القيمة = match (الشي_الذي_نريد_مقارنته) {
//       'قيمة_1' => القيمة_المراد_ارجاعها1,
//        'قيمة_2' => القيمة_المراد_ارجاعها2, 
//        default => القيمة الافتراضية
// };
