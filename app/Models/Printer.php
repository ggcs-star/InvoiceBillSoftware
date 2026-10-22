<?php

namespace App\Models;

use App\Enums\PrinterConnectionType;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use BelongsToBranch, HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'connection_type',
        'ip_address',
        'port',
        'mac_address',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'connection_type' => PrinterConnectionType::class,
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class);
    }

    public function isNetworkPrinter(): bool
    {
        return $this->connection_type === PrinterConnectionType::Network;
    }
}
