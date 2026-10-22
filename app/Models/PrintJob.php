<?php

namespace App\Models;

use App\Enums\PrintJobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintJob extends Model
{
    protected $fillable = [
        'invoice_id',
        'printer_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'status' => PrintJobStatus::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }
}
