<?php

namespace App\Services;

use App\Enums\PrinterConnectionType;
use App\Enums\PrintJobStatus;
use App\Exceptions\PrinterNotConfiguredException;
use App\Exceptions\UnsupportedPrinterConnectionException;
use App\Helpers\ReceiptFormatter;
use App\Models\Invoice;
use App\Models\Printer as PrinterModel;
use App\Models\PrintJob;
use Mike42\Escpos\Printer as Escpos;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

/**
 * Dispatches a formatted receipt to the branch's configured thermal
 * printer. Only network/WiFi printers are reachable directly from this
 * server — a USB or Bluetooth printer is physically attached to the till
 * machine, not to us, so those connection types fall back to the browser
 * print view instead of failing silently.
 */
class PrinterService
{
    public function print(Invoice $invoice, ?PrinterModel $printer = null): PrintJob
    {
        $printer ??= $invoice->branch->defaultPrinter();

        if (! $printer) {
            throw new PrinterNotConfiguredException(
                "No printer is configured for branch [{$invoice->branch->name}]."
            );
        }

        if ($printer->connection_type !== PrinterConnectionType::Network) {
            throw new UnsupportedPrinterConnectionException(
                "Printer [{$printer->name}] is a {$printer->connection_type->label()} printer and cannot be reached "
                . 'from the server directly. Use the browser print view on the till instead.'
            );
        }

        try {
            $this->sendToNetworkPrinter($printer, ReceiptFormatter::build($invoice));

            $invoice->increment('printed_count');
            $invoice->update(['last_printed_at' => now()]);

            return $invoice->printJobs()->create([
                'printer_id' => $printer->id,
                'status' => PrintJobStatus::Success,
            ]);
        } catch (\Throwable $e) {
            return $invoice->printJobs()->create([
                'printer_id' => $printer->id,
                'status' => PrintJobStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function sendToNetworkPrinter(PrinterModel $printer, array $receipt): void
    {
        $connector = new NetworkPrintConnector($printer->ip_address, $printer->port ?: 9100);
        $escpos = new Escpos($connector);

        $escpos->setJustification(Escpos::JUSTIFY_CENTER);
        $escpos->setEmphasis(true);
        $escpos->setTextSize(2, 2);
        $escpos->text($receipt['restaurant']['name'] . "\n");
        $escpos->setTextSize(1, 1);
        $escpos->setEmphasis(false);

        if ($receipt['restaurant']['address']) {
            $escpos->text($receipt['restaurant']['address'] . "\n");
        }
        if ($receipt['restaurant']['phone']) {
            $escpos->text('Ph: ' . $receipt['restaurant']['phone'] . "\n");
        }
        if ($receipt['restaurant']['gstin']) {
            $escpos->text('GSTIN: ' . $receipt['restaurant']['gstin'] . "\n");
        }

        $escpos->feed();
        $escpos->setJustification(Escpos::JUSTIFY_LEFT);
        $escpos->text("Invoice: {$receipt['invoice_number']}\n");
        $escpos->text("Order:   {$receipt['order_number']}\n");
        $escpos->text("Date:    {$receipt['date']}\n");
        if ($receipt['customer_name']) {
            $escpos->text("Customer: {$receipt['customer_name']}\n");
        }
        $escpos->text(str_repeat('-', 32) . "\n");

        foreach ($receipt['items'] as $item) {
            $escpos->text(sprintf("%-20s x%d\n", $item['name'], $item['quantity']));
            $escpos->text(sprintf("%32s\n", number_format((float) $item['line_total'], 2)));
        }

        $escpos->text(str_repeat('-', 32) . "\n");
        $escpos->text(sprintf("%-20s %11s\n", 'Subtotal', number_format((float) $receipt['subtotal'], 2)));
        $escpos->text(sprintf("%-20s %11s\n", 'Tax', number_format((float) $receipt['tax_total'], 2)));

        if ((float) $receipt['discount_total'] > 0) {
            $escpos->text(sprintf("%-20s %11s\n", 'Discount', '-' . number_format((float) $receipt['discount_total'], 2)));
        }

        $escpos->setEmphasis(true);
        $escpos->text(sprintf("%-20s %11s\n", 'TOTAL', number_format((float) $receipt['grand_total'], 2)));
        $escpos->setEmphasis(false);

        foreach ($receipt['payments'] as $payment) {
            $escpos->text(sprintf("Paid via %-10s %9s\n", $payment['method'], number_format((float) $payment['amount'], 2)));
        }

        $escpos->feed();
        $escpos->setJustification(Escpos::JUSTIFY_CENTER);
        $escpos->text("Thank you, visit again!\n");
        $escpos->feed(2);
        $escpos->cut();
        $escpos->close();
    }
}
