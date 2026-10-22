<?php

namespace App\Http\Controllers\Billing;

use App\Helpers\ReceiptFormatter;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function history(Request $request): View
    {
        $branch = $request->user()->branch;

        $invoices = ($branch ? $branch->invoices() : Invoice::query())
            ->with('order')
            ->latest()
            ->paginate(20);

        return view('billing.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $receipt = ReceiptFormatter::build($invoice);

        return view('billing.invoices.show', compact('invoice', 'receipt'));
    }

    public function printView(Invoice $invoice): View
    {
        $this->authorize('print', $invoice);

        $receipt = ReceiptFormatter::build($invoice);

        return view('billing.invoices.print', compact('invoice', 'receipt'));
    }
}
