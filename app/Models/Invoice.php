<?php

namespace App\Models;

use App\Enums\InvoicePaymentStatus;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'order_id',
        'branch_id',
        'invoice_number',
        'subtotal',
        'tax_total',
        'discount_total',
        'grand_total',
        'payment_status',
        'printed_count',
        'last_printed_at',
    ];

    protected $casts = [
        'payment_status' => InvoicePaymentStatus::class,
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'last_printed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class);
    }
}
