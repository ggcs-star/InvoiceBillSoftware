<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'status',
        'subtotal',
        'tax_total',
        'discount_total',
        'grand_total',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Open);
    }

    public function totalPaid(): string
    {
        return (string) $this->payments()->where('status', \App\Enums\PaymentStatus::Success)->sum('amount');
    }
}
