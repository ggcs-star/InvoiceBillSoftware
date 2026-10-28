<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FoodType;
use App\Helpers\SlugGenerator;
use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuItemImportController extends Controller
{
    use AuthorizesBranchAccess;

    private const REQUIRED_COLUMNS = ['item_name', 'category_name', 'price'];

    public function create(): View
    {
        $this->authorize('create', MenuItem::class);

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $branches = Branch::orderBy('name')->get(['id', 'name', 'restaurant_id']);

        return view('admin.menu-items.bulk-import', compact('restaurants', 'branches'));
    }

    public function sample(): StreamedResponse
    {
        $this->authorize('create', MenuItem::class);

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['item_name', 'category_name', 'price', 'description', 'type', 'gst_percentage', 'is_available', 'show_on_customer_site']);
            fputcsv($handle, ['Margherita Pizza', 'Pizzas', '249.00', 'Classic cheese & tomato', 'veg', '5', 'yes', 'yes']);
            fputcsv($handle, ['Chicken Tikka', 'Starters', '220.00', '', 'non_veg', '5', 'yes', 'yes']);
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="menu-items-sample.csv"',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', MenuItem::class);

        $request->validate([
            'restaurant_id' => ['required', 'exists:restaurants,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $branch = Branch::where('id', $request->integer('branch_id'))
            ->where('restaurant_id', $request->integer('restaurant_id'))
            ->firstOrFail();

        $result = $this->processImport($request->file('file'), $branch);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()->route('admin.menu-items.bulk-import')
            ->with('status', "Import complete — {$result['created']} item(s) added, {$result['updated']} updated.");
    }

    /**
     * Branch-scoped bulk import for that branch's own staff login — the
     * branch comes from the route, not a form picker.
     */
    public function createForBranch(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        return view('admin.branches.menu-items-bulk-import', compact('restaurant', 'branch'));
    }

    public function storeForBranch(Request $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $result = $this->processImport($request->file('file'), $branch);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()->route('admin.branches.menu-items.bulk-import', [$restaurant, $branch])
            ->with('status', "Import complete — {$result['created']} item(s) added, {$result['updated']} updated.");
    }

    /**
     * Parses and imports the CSV into the given branch. Returns either the
     * created/updated counts, or a RedirectResponse back with validation
     * errors for the caller to return as-is.
     *
     * @return array{created: int, updated: int}|RedirectResponse
     */
    private function processImport(UploadedFile $file, Branch $branch): array|RedirectResponse
    {
        $rows = $this->readCsv($file);

        if ($rows === null) {
            return back()->withErrors(['file' => 'Could not read that file — make sure it is a valid CSV.']);
        }

        [$header, $dataRows] = $rows;

        $missingColumns = array_diff(self::REQUIRED_COLUMNS, $header);
        if (count($missingColumns) > 0) {
            return back()->withErrors([
                'file' => 'Missing required column(s): ' . implode(', ', $missingColumns) . '. Download the sample file to see the expected format.',
            ]);
        }

        $rowErrors = [];
        foreach ($dataRows as $i => $row) {
            foreach (self::REQUIRED_COLUMNS as $column) {
                if (! isset($row[$column]) || trim((string) $row[$column]) === '') {
                    $rowErrors[] = 'Row ' . ($i + 2) . ": '{$column}' is mandatory but empty.";
                }
            }

            if (isset($row['price']) && trim((string) $row['price']) !== '' && ! is_numeric($row['price'])) {
                $rowErrors[] = 'Row ' . ($i + 2) . ": 'price' must be a number.";
            }
        }

        if ($rowErrors) {
            return back()->withErrors(['file' => $rowErrors])->with('import_errors', $rowErrors);
        }

        [$created, $updated] = DB::transaction(function () use ($dataRows, $branch) {
            $created = 0;
            $updated = 0;

            foreach ($dataRows as $row) {
                $category = $branch->categories()->firstOrCreate(
                    ['slug' => Str::slug($row['category_name'])],
                    ['name' => trim($row['category_name']), 'is_active' => true]
                );

                $foodType = FoodType::tryFrom(Str::lower(trim($row['type'] ?? ''))) ?? FoodType::Veg;
                $slug = SlugGenerator::unique(MenuItem::class, $row['item_name'], ['branch_id' => $branch->id]);

                $existing = $branch->menuItems()->where('slug', Str::slug($row['item_name']))->first();

                $attributes = [
                    'category_id' => $category->id,
                    'name' => trim($row['item_name']),
                    'price' => (float) $row['price'],
                    'gst_percentage' => isset($row['gst_percentage']) && $row['gst_percentage'] !== '' ? (float) $row['gst_percentage'] : 5,
                    'description' => $row['description'] ?? null,
                    'food_type' => $foodType,
                    'is_available' => $this->parseBool($row['is_available'] ?? 'yes'),
                    'show_on_customer_site' => $this->parseBool($row['show_on_customer_site'] ?? 'yes'),
                ];

                if ($existing) {
                    $existing->update($attributes);
                    $updated++;
                } else {
                    $branch->menuItems()->create([...$attributes, 'slug' => $slug]);
                    $created++;
                }
            }

            return [$created, $updated];
        });

        return ['created' => $created, 'updated' => $updated];
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, array<string, string>>}|null
     */
    private function readCsv($file): ?array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return null;
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return null;
        }

        $header = array_map(fn ($col) => trim(Str::lower($col)), $header);

        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            if (count(array_filter($line, fn ($v) => $v !== null && trim((string) $v) !== '')) === 0) {
                continue;
            }
            $rows[] = array_combine($header, array_pad($line, count($header), null));
        }

        fclose($handle);

        return [$header, $rows];
    }

    private function parseBool(string $value): bool
    {
        return in_array(Str::lower(trim($value)), ['yes', 'y', '1', 'true'], true);
    }
}
