<?php

namespace App\Http\Controllers\Api;

use App\Enums\PrintJobStatus;
use App\Exceptions\PrinterNotConfiguredException;
use App\Exceptions\UnsupportedPrinterConnectionException;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\PrinterService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(private readonly PrinterService $printer)
    {
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        $this->authorize('view', $invoice);

        return new InvoiceResource($invoice);
    }

    public function print(Invoice $invoice): JsonResponse
    {
        $this->authorize('print', $invoice);

        try {
            $job = $this->printer->print($invoice);
        } catch (PrinterNotConfiguredException|UnsupportedPrinterConnectionException $e) {
            return response()->json([
                'printed' => false,
                'fallback_url' => route('billing.invoices.print', $invoice),
                'message' => $e->getMessage(),
            ], 422);
        }

        if ($job->status === PrintJobStatus::Failed) {
            return response()->json([
                'printed' => false,
                'fallback_url' => route('billing.invoices.print', $invoice),
                'message' => $job->error_message,
            ], 502);
        }

        return response()->json([
            'printed' => true,
            'invoice' => new InvoiceResource($invoice->refresh()),
        ]);
    }
}
