<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Handle incoming Midtrans Webhook Notification.
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('Midtrans Webhook Received: ', $request->all());

        $payload = $request->all();

        $result = $this->midtransService->handleWebhook($payload);

        // Midtrans requires 200 OK response
        return response()->json([
            'status' => 'OK',
            'result' => $result,
        ], 200);
    }
}
