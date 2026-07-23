<?php

namespace App\Http\Controllers;

use App\Models\OperationalItem;
use App\Models\Unit;
use App\Services\ActivityLogger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class OperationalItemController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm()
                || $user->canAccessEmployeeArea(),
            403
        );

        $query = OperationalItem::query()
            ->with(['unit', 'createdByUser'])
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            if ($unitId) {
                $query->where('unit_id', $unitId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }

            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($user->isAdmin() && $request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('identifier', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query
            ->paginate(10)
            ->withQueryString();

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.items.index', [
            'items' => $items,
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->authorizeItemManager($user);

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.items.create', [
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
            'selectedCategory' => $request->query('category'),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm()
                || $user->canAccessEmployeeArea(),
            403
        );

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf($user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'category' => ['required', 'string', Rule::in(array_keys(OperationalItem::categoryOptions()))],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'identifier' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Jenis item wajib dipilih.',
            'category.in' => 'Jenis item tidak valid.',
            'name.required' => 'Nama item/perangkat wajib diisi.',
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $user->employee?->unit_id;
        }

        $validated['is_active'] = true;
        $validated['created_by_user_id'] = $user->id;

        $item = OperationalItem::create($validated);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Create Operational Item',
            'Menambahkan item operasional: ' . $item->name,
            $item,
            null,
            [
                'category' => $item->category,
                'name' => $item->name,
                'unit_id' => $item->unit_id,
                'location' => $item->location,
            ]
        );

        return redirect()
            ->route('operations.items.index', ['category' => $item->category])
            ->with('success', 'Item operasional berhasil ditambahkan.');
    }

    public function edit(Request $request, OperationalItem $item)
    {
        $user = $request->user();

        $this->authorizeItemManage($user, $item);

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.items.edit', [
            'item' => $item,
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
        ]);
    }

    public function update(Request $request, OperationalItem $item)
    {
        $user = $request->user();

        $this->authorizeItemManage($user, $item);

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf($user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'category' => ['required', 'string', Rule::in(array_keys(OperationalItem::categoryOptions()))],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'identifier' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Jenis item wajib dipilih.',
            'category.in' => 'Jenis item tidak valid.',
            'name.required' => 'Nama item/perangkat wajib diisi.',
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $item->unit_id;
        }

        $oldValues = [
            'unit_id' => $item->unit_id,
            'category' => $item->category,
            'name' => $item->name,
            'location' => $item->location,
            'brand' => $item->brand,
            'model' => $item->model,
            'year' => $item->year,
            'quantity' => $item->quantity,
            'identifier' => $item->identifier,
            'description' => $item->description,
        ];

        $item->update([
            'unit_id' => $validated['unit_id'] ?? null,
            'category' => $validated['category'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'model' => $validated['model'] ?? null,
            'year' => $validated['year'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'identifier' => $validated['identifier'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Update Operational Item',
            'Memperbarui item operasional: ' . $item->name,
            $item,
            $oldValues,
            [
                'unit_id' => $item->unit_id,
                'category' => $item->category,
                'name' => $item->name,
                'location' => $item->location,
                'brand' => $item->brand,
                'model' => $item->model,
                'year' => $item->year,
                'quantity' => $item->quantity,
                'identifier' => $item->identifier,
                'description' => $item->description,
            ]
        );

        return redirect()
            ->route('operations.items.index', ['category' => $item->category])
            ->with('success', 'Item operasional berhasil diperbarui.');
    }

    public function toggleActive(Request $request, OperationalItem $item)
    {
        $user = $request->user();

        $this->authorizeItemManage($user, $item);

        $oldValues = [
            'is_active' => $item->is_active,
        ];

        $item->update([
            'is_active' => ! $item->is_active,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Toggle Operational Item Status',
            'Mengubah status item operasional: ' . $item->name,
            $item,
            $oldValues,
            [
                'is_active' => $item->is_active,
            ]
        );

        return back()->with('success', 'Status item berhasil diperbarui.');
    }

    public function importForm(Request $request)
    {
        $user = $request->user();

        $this->authorizeItemManager($user);

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.items.import', [
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
            'selectedCategory' => $request->query('category'),
        ]);
    }

    public function import(Request $request)
    {
        $user = $request->user();

        $this->authorizeItemManager($user);

        $validated = $request->validate([
            'unit_id' => [
                Rule::requiredIf($user->isAdmin()),
                'nullable',
                'exists:units,id',
            ],
            'category' => ['required', 'string', Rule::in(array_keys(OperationalItem::categoryOptions()))],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Jenis item wajib dipilih.',
            'category.in' => 'Jenis item tidak valid.',
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat xlsx, xls, atau csv.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $user->employee?->unit_id;
        }

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());

        $rows = $this->extractOperationalItemsFromSpreadsheet(
            spreadsheet: $spreadsheet,
            category: $validated['category'],
        );

        if ($rows->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Tidak ada data item yang bisa dibaca dari file Excel.');
        }

        $importedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($rows, $validated, $user, &$importedCount, &$skippedCount) {
            foreach ($rows as $row) {
                $exists = OperationalItem::query()
                    ->where('unit_id', $validated['unit_id'])
                    ->where('category', $validated['category'])
                    ->where('name', $row['name'])
                    ->where('location', $row['location'])
                    ->where('identifier', $row['identifier'])
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                OperationalItem::create([
                    'unit_id' => $validated['unit_id'],
                    'category' => $validated['category'],
                    'name' => $row['name'],
                    'location' => $row['location'],
                    'brand' => $row['brand'],
                    'model' => $row['model'],
                    'year' => $row['year'],
                    'quantity' => $row['quantity'],
                    'identifier' => $row['identifier'],
                    'description' => $row['description'],
                    'is_active' => true,
                    'created_by_user_id' => $user->id,
                ]);

                $importedCount++;
            }
        });

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Import Operational Items',
            'Import master item operasional dari Excel',
            null,
            null,
            [
                'category' => $validated['category'],
                'unit_id' => $validated['unit_id'],
                'imported_count' => $importedCount,
                'skipped_count' => $skippedCount,
            ]
        );

        return redirect()
            ->route('operations.items.index', ['category' => $validated['category']])
            ->with('success', 'Import selesai. Berhasil: ' . $importedCount . ' item. Duplikat/skip: ' . $skippedCount . ' item.');
    }

    private function extractOperationalItemsFromSpreadsheet($spreadsheet, string $category)
    {
        $items = collect();

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $sheetName = trim($sheet->getTitle());
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                continue;
            }

            $headerRowNumber = $this->detectHeaderRow($rows, $category);

            if (! $headerRowNumber) {
                continue;
            }

            $headers = $this->normalizeHeaders($rows[$headerRowNumber]);

            foreach ($rows as $rowNumber => $row) {
                if ($rowNumber <= $headerRowNumber) {
                    continue;
                }

                $mapped = $this->mapSpreadsheetRowToOperationalItem(
                    row: $row,
                    headers: $headers,
                    sheetName: $sheetName,
                    category: $category,
                );

                if (! $mapped) {
                    continue;
                }

                $items->push($mapped);
            }
        }

        return $items
            ->filter(fn ($item) => filled($item['name']))
            ->values();
    }

    private function detectHeaderRow(array $rows, string $category): ?int
    {
        foreach ($rows as $rowNumber => $row) {
            $text = collect($row)
                ->filter()
                ->map(fn ($value) => $this->normalizeHeaderText((string) $value))
                ->implode(' ');

            if ($category === OperationalItem::CATEGORY_NETWORK) {
                if (
                    str_contains($text, 'nama perangkat')
                    || str_contains($text, 'merk access point')
                    || str_contains($text, 'access point')
                ) {
                    return (int) $rowNumber;
                }
            }

            if ($category === OperationalItem::CATEGORY_LAB_INVENTORY) {
                if (
                    str_contains($text, 'nama')
                    && str_contains($text, 'identifikasi')
                    && str_contains($text, 'jumlah')
                    && str_contains($text, 'kondisi')
                ) {
                    return (int) $rowNumber;
                }

                if (
                    str_contains($text, 'sub kelompok')
                    && str_contains($text, 'merk')
                    && str_contains($text, 'type')
                ) {
                    return (int) $rowNumber - 1;
                }
            }

            if ($category === OperationalItem::CATEGORY_LAB_CHECK) {
                if (
                    str_contains($text, 'no pc')
                    || (
                        str_contains($text, 'os')
                        && str_contains($text, 'mouse')
                        && str_contains($text, 'keyboard')
                    )
                ) {
                    return (int) $rowNumber;
                }
            }
        }

        return null;
    }

    private function normalizeHeaders(array $row): array
    {
        $headers = [];

        foreach ($row as $column => $value) {
            $headers[$column] = $this->normalizeHeaderText((string) $value);
        }

        return $headers;
    }

    private function normalizeHeaderText(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(["\n", "\r", "\t"], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        $replacements = [
            '/' => ' ',
            '-' => ' ',
            '_' => ' ',
            '.' => '',
            ':' => '',
            '(' => '',
            ')' => '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $value);
    }

    private function valueByHeader(array $row, array $headers, array $keywords): ?string
    {
        foreach ($headers as $column => $header) {
            foreach ($keywords as $keyword) {
                if (str_contains($header, $keyword)) {
                    $value = $row[$column] ?? null;

                    if (filled($value)) {
                        return trim((string) $value);
                    }
                }
            }
        }

        return null;
    }

    private function mapSpreadsheetRowToOperationalItem(array $row, array $headers, string $sheetName, string $category): ?array
    {
        if ($category === OperationalItem::CATEGORY_NETWORK) {
            $name = $this->valueByHeader($row, $headers, ['nama perangkat', 'access point', 'merk access point'])
                ?: $this->valueByHeader($row, $headers, ['merk']);

            $brand = $this->valueByHeader($row, $headers, ['merk', 'access point']);
            $model = $this->valueByHeader($row, $headers, ['model']);
            $location = $this->valueByHeader($row, $headers, ['lokasi']) ?: $sheetName;
            $year = $this->valueByHeader($row, $headers, ['tahun']);
            $description = $this->valueByHeader($row, $headers, ['keterangan']);

            if (blank($name) || $this->isLikelyNonDataRow($name)) {
                return null;
            }

            return [
                'name' => $name,
                'location' => $location,
                'brand' => $brand,
                'model' => $model,
                'year' => $year,
                'quantity' => 1,
                'identifier' => null,
                'description' => $description,
            ];
        }

        if ($category === OperationalItem::CATEGORY_LAB_INVENTORY) {
            // Format inventaris lab memakai header 2 baris, jadi mapping paling aman pakai posisi kolom.
            $name = trim((string) ($row['B'] ?? ''));
            $specification = trim((string) ($row['C'] ?? ''));
            $brand = trim((string) ($row['D'] ?? ''));
            $model = trim((string) ($row['E'] ?? ''));
            $year = trim((string) ($row['F'] ?? ''));
            $quantityRaw = trim((string) ($row['G'] ?? ''));
            $goodCondition = trim((string) ($row['H'] ?? ''));
            $brokenCondition = trim((string) ($row['I'] ?? ''));
            $identifier = trim((string) ($row['J'] ?? ''));

            if (blank($name) || $this->isLikelyNonDataRow($name)) {
                return null;
            }

            // Skip baris nomor panduan seperti 1, 2, 3a, 3b, 4, 5, 6, 7.
            if (is_numeric($name)) {
                return null;
            }

            $quantity = $this->extractIntegerFromText($quantityRaw);

            $descriptionParts = [];

            if (filled($specification)) {
                $descriptionParts[] = 'Spesifikasi: ' . $specification;
            }

            if (filled($goodCondition)) {
                $descriptionParts[] = 'Kondisi baik: ' . $goodCondition;
            }

            if (filled($brokenCondition)) {
                $descriptionParts[] = 'Kondisi rusak: ' . $brokenCondition;
            }

            return [
                'name' => $name,
                'location' => $sheetName,
                'brand' => filled($brand) ? $brand : null,
                'model' => filled($model) ? $model : null,
                'year' => filled($year) ? $year : null,
                'quantity' => $quantity ?: 1,
                'identifier' => filled($identifier) ? $identifier : null,
                'description' => filled($descriptionParts) ? implode("\n", $descriptionParts) : null,
            ];
        }

        if ($category === OperationalItem::CATEGORY_LAB_CHECK) {
            $identifier = $this->valueByHeader($row, $headers, ['no pc', 'nomor pc']);
            $name = $identifier ?: $this->valueByHeader($row, $headers, ['nama perangkat', 'pc']);

            $location = $sheetName;
            $description = $this->valueByHeader($row, $headers, ['keterangan']);

            if (blank($name) || $this->isLikelyNonDataRow($name)) {
                return null;
            }

            return [
                'name' => $name,
                'location' => $location,
                'brand' => null,
                'model' => null,
                'year' => null,
                'quantity' => 1,
                'identifier' => $identifier,
                'description' => $description,
            ];
        }

        return null;
    }

    private function isLikelyNonDataRow(?string $value): bool
    {
        if (blank($value)) {
            return true;
        }

        $normalized = $this->normalizeHeaderText($value);

        $blocked = [
            'no',
            'nama',
            'nama perangkat',
            'nama sub kelompok',
            'total',
            'jumlah',
            'keterangan',
            'mengetahui',
            'kepala unit',
            'unit teknologi informasi',
            'sub kelompok',
            'identifikasi',
            'identifikasi spesifikasi',
            'merk',
            'type',
            'tipe',
            'tahun',
            'tahun perolehan',
            'barang',
            'baik',
            'rusak',
        ];

        return in_array($normalized, $blocked, true);
    }

    private function extractIntegerFromText(?string $value): ?int
    {
        if (blank($value)) {
            return null;
        }

        if (preg_match('/\d+/', $value, $matches)) {
            return (int) $matches[0];
        }

        return null;
    }

    private function authorizeItemManager($user): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm(),
            403,
            'Anda tidak memiliki akses untuk mengelola master item operasional.'
        );

        if (! $user->isAdmin()) {
            abort_if(
                blank($user->employee?->unit_id),
                403,
                'Akun belum terhubung dengan unit pegawai.'
            );
        }
    }

    private function authorizeItemManage($user, OperationalItem $item): void
    {
        $this->authorizeItemManager($user);

        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            (int) $item->unit_id === (int) $user->employee?->unit_id,
            403,
            'Anda tidak memiliki akses ke item ini.'
        );
    }
}