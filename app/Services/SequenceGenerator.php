<?php

namespace App\Services;

use App\Enums\SequenceType;
use App\Models\Branch;
use App\Models\NumberSequence;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Generates gap-free, human-readable sequential numbers (e.g. INV-2026-00125)
 * scoped per branch + year, safe under concurrent requests via row locking.
 */
class SequenceGenerator
{
    public function next(Branch|int $branch, SequenceType $type): string
    {
        $branchId = $branch instanceof Branch ? $branch->id : $branch;
        $year = (int) now()->format('Y');

        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $number = DB::transaction(function () use ($branchId, $type, $year) {
                    $sequence = NumberSequence::query()
                        ->where('branch_id', $branchId)
                        ->where('type', $type->value)
                        ->where('year', $year)
                        ->lockForUpdate()
                        ->first();

                    $sequence ??= NumberSequence::create([
                        'branch_id' => $branchId,
                        'type' => $type,
                        'year' => $year,
                        'last_number' => 0,
                    ]);

                    $sequence->increment('last_number');

                    return $sequence->last_number;
                });

                return sprintf('%s-%d-%05d', $type->prefix(), $year, $number);
            } catch (QueryException $e) {
                if ($attempt === 0 && str_contains($e->getMessage(), 'Duplicate entry')) {
                    continue;
                }

                throw $e;
            }
        }

        throw new \RuntimeException('Unable to generate a sequence number.');
    }
}
