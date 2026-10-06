<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessIncomingWebhook;
use App\Models\Clint;
use App\Models\Webhooks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BankWebhookController extends Controller {


    private const SUPPORTED_BANKS = [
        'paytech',
        'acme',
    ];



    public function store( Request $request,string $bank, Clint $clint ): Response {

        $bank = strtolower(trim($bank));

        abort_unless(
            in_array($bank, self::SUPPORTED_BANKS, true),
            Response::HTTP_NOT_FOUND,
            'Unsupported bank.'
        );

        $payload = trim($request->getContent());

        if ($payload === '') {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><response><message>Webhook payload is required.</message></response>';
            return response($xml, Response::HTTP_UNPROCESSABLE_ENTITY)->header('Content-Type', 'application/xml');
        }

        $webhook = DB::transaction(function () use ($bank, $clint, $payload) {

                $webhook = Webhooks::create([
                    'clint_id' => $clint->id,
                    'bank'      => $bank,
                    'payload'   => $payload,
                    'status'    => 'done',
                ]);

                ProcessIncomingWebhook::dispatch($webhook)->afterCommit();

                return $webhook;
            }

        );



        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<response>
    <message>Webhook accepted successfully.</message>
    <data>
        <webhook_id>' . $webhook->id . '</webhook_id>
        <bank>' . htmlspecialchars($webhook->bank ?? '') . '</bank>
        <clint_id>' . $webhook->clint_id . '</clint_id>
        <status>' . htmlspecialchars($webhook->status ?? '') . '</status>
        <received_at>' . $webhook->created_at . '</received_at>
    </data>
    <payload>' . htmlspecialchars($webhook->payload ?? '') . '</payload>
</response>';

        return response($xml, Response::HTTP_ACCEPTED)->header('Content-Type', 'application/xml');
    }
}



















/*


20250615156,50#202506159000001#note/debt payment march/internal_reference/5c22c2a6-867e-353e-a516-19e20c7f5839





20250615156 = تاريخ المعاملة
202506159000001 = الرقم المرجعي
156,50 = المبلغ
note/debt payment march الملاحظات
internal_reference/5c22c2a6-867e-353e-a516-19e20c7f5839 = المرجع الخاص بالعميل




*/
