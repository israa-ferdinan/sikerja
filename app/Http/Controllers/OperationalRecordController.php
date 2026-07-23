<?php

namespace App\Http\Controllers;

use App\Models\OperationalItem;
use App\Models\OperationalRecord;
use App\Models\OperationalRecordItem;
use App\Models\Unit;
use App\Models\Employee;
use App\Models\User;
use App\Models\Duty;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Services\ActivityLogger;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Symfony\Component\HttpFoundation\StreamedResponse;


class OperationalRecordController extends Controller
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

        $query = OperationalRecord::query()
            ->with(['unit', 'createdByUser', 'verifiedByUser'])
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            if ($unitId) {
                $query->where('unit_id', $unitId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $baseQuery = clone $query;

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', OperationalRecord::STATUS_DRAFT)->count(),
            'submitted' => (clone $baseQuery)->where('status', OperationalRecord::STATUS_SUBMITTED)->count(),
            'verified' => (clone $baseQuery)->where('status', OperationalRecord::STATUS_VERIFIED)->count(),
            'cancelled' => (clone $baseQuery)->where('status', OperationalRecord::STATUS_CANCELLED)->count(),
            'bulan_ini' => (clone $baseQuery)
                ->where('period_month', now()->month)
                ->where('period_year', now()->year)
                ->count(),
        ];

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('period_month', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->where('period_year', (int) $request->year);
        }

        if ($user->isAdmin() && $request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('record_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $records = $query
            ->paginate(10)
            ->withQueryString();

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        return view('operations.records.index', [
            'records' => $records,
            'summary' => $summary,
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
            'statusOptions' => OperationalRecord::statusOptions(),
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm()
                || $user->canAccessEmployeeArea(),
            403
        );

        $units = $user->isAdmin()
            ? Unit::query()->orderBy('name')->get()
            : collect();

        $selectedCategory = $request->query('category');

        $sourceRecordsQuery = OperationalRecord::query()
            ->with('unit')
            ->latest();

        if (! $user->isAdmin()) {
            $unitId = $user->employee?->unit_id;

            if ($unitId) {
                $sourceRecordsQuery->where('unit_id', $unitId);
            } else {
                $sourceRecordsQuery->whereRaw('1 = 0');
            }
        }

        if ($selectedCategory) {
            $sourceRecordsQuery->where('category', $selectedCategory);
        }

        $sourceRecords = $sourceRecordsQuery
            ->limit(30)
            ->get();

        $unitId = $user->isAdmin()
            ? $request->query('unit_id')
            : $user->employee?->unit_id;

        $technicians = $user->isAdmin()
            ? Employee::query()
                ->whereNotNull('unit_id')
                ->orderBy('unit_id')
                ->orderBy('name')
                ->get()
            : $this->technicianOptionsForUnit($unitId);

        $defaultTechnicianId = $this->resolveDefaultTechnicianEmployeeId(
            unitId: $unitId ? (int) $unitId : null,
            category: $selectedCategory,
        );

        return view('operations.records.create', [
            'units' => $units,
            'categoryOptions' => OperationalItem::categoryOptions(),
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
            'selectedCategory' => $selectedCategory,
            'sourceRecords' => $sourceRecords,
            'technicians' => $technicians,
            'defaultTechnicianId' => $defaultTechnicianId,
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
            'title' => ['required', 'string', 'max:255'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:' . (now()->year + 2)],
            'record_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'source_mode' => ['required', 'string', Rule::in(['master', 'previous'])],
            'source_record_id' => ['nullable', 'exists:operational_records,id'],
            'technician_employee_id' => ['nullable', 'exists:employees,id'],
        ], [
            'unit_id.required' => 'Unit wajib dipilih.',
            'unit_id.exists' => 'Unit tidak valid.',
            'category.required' => 'Jenis rekap wajib dipilih.',
            'category.in' => 'Jenis rekap tidak valid.',
            'title.required' => 'Judul rekap wajib diisi.',
            'period_month.required' => 'Bulan wajib dipilih.',
            'period_year.required' => 'Tahun wajib dipilih.',
            'source_mode.required' => 'Sumber data rekap wajib dipilih.',
            'source_mode.in' => 'Sumber data rekap tidak valid.',
            'source_record_id.exists' => 'Rekap sumber tidak valid.',
            'technician_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        if (! $user->isAdmin()) {
            $validated['unit_id'] = $user->employee?->unit_id;
        }

        $unitId = $validated['unit_id'] ?? null;

        $technicianId = $validated['technician_employee_id'] ?? null;

        if ($technicianId) {
            $technicianIsValid = Employee::query()
                ->where('id', $technicianId)
                ->where('unit_id', $unitId)
                ->exists();

            if (! $technicianIsValid) {
                return back()
                    ->withInput()
                    ->with('error', 'Teknisi yang dipilih tidak sesuai dengan unit rekap.');
            }
        }

        if (! $technicianId) {
            $technicianId = $this->resolveDefaultTechnicianEmployeeId(
                unitId: $unitId,
                category: $validated['category'],
            );
        }

        $sourceRecord = null;

        if ($validated['source_mode'] === 'previous') {
            $sourceRecord = $this->resolveSourceRecord(
                user: $user,
                unitId: $unitId,
                category: $validated['category'],
                sourceRecordId: $validated['source_record_id'] ?? null,
            );
        }

        $activeItems = OperationalItem::query()
            ->active()
            ->where('category', $validated['category'])
            ->where('unit_id', $unitId)
            ->orderBy('location')
            ->orderBy('name')
            ->get();

        if (! $sourceRecord && $activeItems->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Belum ada rekap sumber atau item aktif untuk jenis rekap ini. Tambahkan item operasional terlebih dahulu.');
        }

        $record = DB::transaction(function () use ($validated, $activeItems, $sourceRecord, $user, $technicianId) {
            $record = OperationalRecord::create([
                'unit_id' => $validated['unit_id'] ?? null,
                'category' => $validated['category'],
                'title' => $validated['title'],
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year'],
                'record_date' => $validated['record_date'] ?? now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
                'technician_employee_id' => $technicianId,
                'created_by_user_id' => $user->id,
                'updated_by_user_id' => $user->id,
            ]);

            $copiedItemIds = collect();

            if ($sourceRecord) {
                $sourceRecord->load('items');

                foreach ($sourceRecord->items as $sourceItem) {
                    OperationalRecordItem::create([
                        'operational_record_id' => $record->id,
                        'operational_item_id' => $sourceItem->operational_item_id,
                        'item_name' => $sourceItem->item_name,
                        'item_location' => $sourceItem->item_location,
                        'item_identifier' => $sourceItem->item_identifier,
                        'condition_status' => $sourceItem->condition_status,
                        'component_status' => $sourceItem->component_status,
                        'description' => $sourceItem->description,
                        'action_taken' => $sourceItem->action_taken,
                    ]);

                    if (filled($sourceItem->operational_item_id)) {
                        $copiedItemIds->push((int) $sourceItem->operational_item_id);
                    }
                }
            }

            $newActiveItems = $activeItems
                ->reject(fn ($item) => $copiedItemIds->contains((int) $item->id));

            foreach ($newActiveItems as $item) {
                OperationalRecordItem::create([
                    'operational_record_id' => $record->id,
                    'operational_item_id' => $item->id,
                    'item_name' => $item->name,
                    'item_location' => $item->location,
                    'item_identifier' => $item->identifier,
                    'condition_status' => OperationalRecordItem::CONDITION_NORMAL,
                    'component_status' => $this->defaultComponentStatus($record->category),
                    'description' => null,
                    'action_taken' => null,
                ]);
            }

            ActivityLogger::log(
                'Operasional SIM/TI',
                'Create Operational Record',
                'Membuat rekap operasional: ' . $record->title,
                $record,
                null,
                [
                    'record_code' => $record->record_code,
                    'category' => $record->category,
                    'unit_id' => $record->unit_id,
                    'source_mode' => $validated['source_mode'],
                    'source_record_id' => $sourceRecord?->id,
                    'copied_item_count' => $sourceRecord ? $sourceRecord->items->count() : 0,
                    'new_item_count' => $newActiveItems->count(),
                ]
            );

            return $record;
        });

        $itemCount = $record->items()->count();

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Rekap operasional berhasil dibuat dengan ' . $itemCount . ' item.');
    }

    public function show(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordAccess($user, $record);

        $record->load([
            'unit',
            'createdBy',
            'updatedBy',
            'verifiedBy',
            'cancelledBy',
            'technician',
            'items.item',
        ]);

        return view('operations.records.show', [
            'record' => $record,
            'conditionOptions' => OperationalRecordItem::conditionOptions(),
            'componentOptions' => OperationalRecordItem::componentOptions(),
            'labComponentKeys' => OperationalRecordItem::labComponentKeys(),
            'monthOptions' => $this->monthOptions(),
        ]);
    }

    public function updateItem(Request $request, OperationalRecord $record, OperationalRecordItem $item)
    {
        $user = $request->user();

        $this->authorizeRecordAccess($user, $record);

        abort_unless(
            (int) $item->operational_record_id === (int) $record->id,
            404
        );

        abort_if(
            ! $record->isEditable(),
            403,
            'Rekap yang sudah diajukan, diverifikasi, atau dibatalkan tidak bisa diubah.'
        );

        $validated = $request->validate([
            'condition_status' => ['required', 'string', Rule::in(array_keys(OperationalRecordItem::conditionOptions()))],
            'component_status' => ['nullable', 'array'],
            'component_status.*' => ['nullable', 'string', Rule::in(array_keys(OperationalRecordItem::componentOptions()))],
            'description' => ['nullable', 'string', 'max:3000'],
            'action_taken' => ['nullable', 'string', 'max:3000'],
        ], [
            'condition_status.required' => 'Kondisi wajib dipilih.',
            'condition_status.in' => 'Kondisi tidak valid.',
            'component_status.*.in' => 'Status komponen tidak valid.',
        ]);

        $oldValues = [
            'condition_status' => $item->condition_status,
            'component_status' => $item->component_status,
            'description' => $item->description,
            'action_taken' => $item->action_taken,
        ];

        $componentStatus = null;

        if ($record->category === OperationalItem::CATEGORY_LAB_CHECK) {
            $componentStatus = [];

            foreach (OperationalRecordItem::labComponentKeys() as $key => $label) {
                $componentStatus[$key] = $validated['component_status'][$key]
                    ?? OperationalRecordItem::COMPONENT_GOOD;
            }
        }

        $item->update([
            'condition_status' => $validated['condition_status'],
            'component_status' => $componentStatus,
            'description' => $validated['description'] ?? null,
            'action_taken' => $validated['action_taken'] ?? null,
        ]);

        $record->update([
            'updated_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Update Operational Record Item',
            'Memperbarui item rekap operasional: ' . $item->item_name . ' pada ' . $record->record_code,
            $item,
            $oldValues,
            [
                'condition_status' => $item->condition_status,
                'component_status' => $item->component_status,
                'description' => $item->description,
                'action_taken' => $item->action_taken,
            ]
        );

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Item rekap berhasil diperbarui.');
    }

    public function storeItem(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordAccess($user, $record);

        abort_if(
            ! $record->isEditable(),
            403,
            'Item hanya bisa ditambahkan pada rekap Draft.'
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'identifier' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'condition_status' => ['required', 'string', Rule::in(array_keys(OperationalRecordItem::conditionOptions()))],
            'action_taken' => ['nullable', 'string', 'max:3000'],
        ], [
            'name.required' => 'Nama item/perangkat wajib diisi.',
            'condition_status.required' => 'Kondisi wajib dipilih.',
            'condition_status.in' => 'Kondisi tidak valid.',
        ]);

        $newItem = DB::transaction(function () use ($validated, $record, $user) {
            $operationalItem = OperationalItem::create([
                'unit_id' => $record->unit_id,
                'category' => $record->category,
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'brand' => $validated['brand'] ?? null,
                'model' => $validated['model'] ?? null,
                'year' => $validated['year'] ?? null,
                'quantity' => $validated['quantity'] ?? 1,
                'identifier' => $validated['identifier'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
                'created_by_user_id' => $user->id,
            ]);

            $componentStatus = $this->defaultComponentStatus($record->category);

            $recordItem = OperationalRecordItem::create([
                'operational_record_id' => $record->id,
                'operational_item_id' => $operationalItem->id,
                'item_name' => $operationalItem->name,
                'item_location' => $operationalItem->location,
                'item_identifier' => $operationalItem->identifier,
                'condition_status' => $validated['condition_status'],
                'component_status' => $componentStatus,
                'description' => $validated['description'] ?? null,
                'action_taken' => $validated['action_taken'] ?? null,
            ]);

            $record->update([
                'updated_by_user_id' => $user->id,
            ]);

            ActivityLogger::log(
                'Operasional SIM/TI',
                'Create Operational Item From Record',
                'Menambahkan item baru dari rekap operasional: ' . $operationalItem->name . ' pada ' . $record->record_code,
                $recordItem,
                null,
                [
                    'record_id' => $record->id,
                    'record_code' => $record->record_code,
                    'operational_item_id' => $operationalItem->id,
                    'record_item_id' => $recordItem->id,
                    'category' => $record->category,
                    'name' => $operationalItem->name,
                    'unit_id' => $operationalItem->unit_id,
                ]
            );

            return $operationalItem;
        });

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Item baru berhasil ditambahkan ke master dan rekap: ' . $newItem->name);
    }

    public function submit(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordAccess($user, $record);

        abort_unless(
            $record->canSubmit(),
            403,
            'Hanya rekap Draft yang bisa diajukan.'
        );

        $oldValues = [
            'status' => $record->status,
            'submitted_at' => $record->submitted_at,
        ];

        $record->update([
            'status' => OperationalRecord::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'updated_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Submit Operational Record',
            'Mengajukan rekap operasional: ' . $record->record_code,
            $record,
            $oldValues,
            [
                'status' => $record->status,
                'submitted_at' => $record->submitted_at,
                'updated_by_user_id' => $record->updated_by_user_id,
            ]
        );

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Rekap operasional berhasil diajukan.');
    }

    public function verify(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordManage($user, $record);

        abort_unless(
            $record->canVerify(),
            403,
            'Hanya rekap Diajukan yang bisa diverifikasi.'
        );

        $oldValues = [
            'status' => $record->status,
            'verified_by_user_id' => $record->verified_by_user_id,
            'verified_at' => $record->verified_at,
        ];

        $record->update([
            'status' => OperationalRecord::STATUS_VERIFIED,
            'verified_by_user_id' => $user->id,
            'verified_at' => now(),
            'updated_by_user_id' => $user->id,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Verify Operational Record',
            'Memverifikasi rekap operasional: ' . $record->record_code,
            $record,
            $oldValues,
            [
                'status' => $record->status,
                'verified_by_user_id' => $record->verified_by_user_id,
                'verified_at' => $record->verified_at,
                'updated_by_user_id' => $record->updated_by_user_id,
            ]
        );

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Rekap operasional berhasil diverifikasi.');
    }

    public function cancel(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordManage($user, $record);

        abort_unless(
            $record->canCancel(),
            403,
            'Rekap ini tidak bisa dibatalkan.'
        );

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldValues = [
            'status' => $record->status,
            'cancelled_by_user_id' => $record->cancelled_by_user_id,
            'cancelled_at' => $record->cancelled_at,
            'notes' => $record->notes,
        ];

        $notes = $record->notes;

        if (filled($validated['cancel_reason'] ?? null)) {
            $notes = trim(($notes ? $notes . "\n\n" : '') . 'Alasan pembatalan: ' . $validated['cancel_reason']);
        }

        $record->update([
            'status' => OperationalRecord::STATUS_CANCELLED,
            'cancelled_by_user_id' => $user->id,
            'cancelled_at' => now(),
            'updated_by_user_id' => $user->id,
            'notes' => $notes,
        ]);

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Cancel Operational Record',
            'Membatalkan rekap operasional: ' . $record->record_code,
            $record,
            $oldValues,
            [
                'status' => $record->status,
                'cancelled_by_user_id' => $record->cancelled_by_user_id,
                'cancelled_at' => $record->cancelled_at,
                'updated_by_user_id' => $record->updated_by_user_id,
            ]
        );

        return redirect()
            ->route('operations.forms.show', $record)
            ->with('success', 'Rekap operasional berhasil dibatalkan.');
    }

    public function destroy(Request $request, OperationalRecord $record)
    {
        $user = $request->user();

        $this->authorizeRecordManage($user, $record);

        abort_unless(
            $record->isDeletable(),
            403,
            'Hanya rekap Draft atau Dibatalkan yang bisa dihapus.'
        );

        $oldValues = [
            'record_code' => $record->record_code,
            'category' => $record->category,
            'title' => $record->title,
            'status' => $record->status,
            'unit_id' => $record->unit_id,
        ];

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Delete Operational Record',
            'Menghapus rekap operasional: ' . $record->record_code,
            $record,
            $oldValues,
            null
        );

        $record->delete();

        return redirect()
            ->route('operations.forms.index')
            ->with('success', 'Rekap operasional berhasil dihapus.');
    }

    public function exportExcel(Request $request, OperationalRecord $record): StreamedResponse
    {
        $user = $request->user();

        $this->authorizeRecordAccess($user, $record);

        $record->load(['unit', 'items.item', 'technician', 'createdBy.employee']);

        $templatePath = $this->templatePathForRecord($record);

        if ($templatePath && file_exists($templatePath)) {
            $spreadsheet = IOFactory::load($templatePath);

            if ($record->category === OperationalItem::CATEGORY_NETWORK) {
                $this->fillNetworkTemplate($spreadsheet, $record);
            } elseif ($record->category === OperationalItem::CATEGORY_LAB_INVENTORY) {
                $this->fillLabInventoryTemplate($spreadsheet, $record);
            } elseif ($record->category === OperationalItem::CATEGORY_LAB_CHECK) {
                $this->fillLabCheckTemplate($spreadsheet, $record);
            } else {
                $this->buildGenericExportSheet($spreadsheet, $record);
            }
        } else {
            $spreadsheet = new Spreadsheet();
            $this->buildGenericExportSheet($spreadsheet, $record);
        }

        $fileName = $this->safeExportFileName($record->record_code . '-' . $record->title . '.xlsx');

        ActivityLogger::log(
            'Operasional SIM/TI',
            'Export Operational Record Excel',
            'Export Excel rekap operasional berbasis template: ' . $record->record_code,
            $record,
            null,
            [
                'record_code' => $record->record_code,
                'category' => $record->category,
                'file_name' => $fileName,
                'template_used' => $templatePath ? basename($templatePath) : null,
            ]
        );

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function monthOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function yearOptions(): array
    {
        $currentYear = now()->year;

        return range($currentYear - 2, $currentYear + 1);
    }

    private function defaultComponentStatus(string $category): ?array
    {
        if ($category !== OperationalItem::CATEGORY_LAB_CHECK) {
            return null;
        }

        return collect(OperationalRecordItem::labComponentKeys())
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => OperationalRecordItem::COMPONENT_GOOD])
            ->toArray();
    }

    private function resolveSourceRecord($user, ?int $unitId, string $category, ?int $sourceRecordId): ?OperationalRecord
    {
        $query = OperationalRecord::query()
            ->where('category', $category)
            ->where('unit_id', $unitId)
            ->latest();

        if ($sourceRecordId) {
            $query->where('id', $sourceRecordId);
        }

        $record = $query->first();

        if (! $record) {
            return null;
        }

        if (! $user->isAdmin()) {
            abort_unless(
                $record->unit_id === $user->employee?->unit_id,
                403,
                'Anda tidak memiliki akses ke rekap sumber ini.'
            );
        }

        return $record;
    }

    private function authorizeRecordAccess($user, OperationalRecord $record): void
    {
        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            $record->unit_id === $user->employee?->unit_id,
            403,
            'Anda tidak memiliki akses ke rekap ini.'
        );
    }

    private function authorizeRecordManage($user, OperationalRecord $record): void
    {
        abort_unless(
            $user->isAdmin()
                || $user->isKanit()
                || $user->isGkm(),
            403,
            'Anda tidak memiliki akses untuk mengelola rekap ini.'
        );

        if ($user->isAdmin()) {
            return;
        }

        abort_unless(
            (int) $record->unit_id === (int) $user->employee?->unit_id,
            403,
            'Anda tidak memiliki akses untuk mengelola rekap unit lain.'
        );
    }

    private function buildNetworkExportSheet(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Jaringan');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', strtoupper($record->title));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Periode: ' . $this->recordPeriodLabel($record));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'A4' => 'NO',
            'B4' => 'MERK / ACCESS POINT',
            'C4' => 'NAMA PERANGKAT',
            'D4' => 'MODEL',
            'E4' => 'LOKASI',
            'F4' => 'TAHUN',
            'G4' => 'KETERANGAN',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $row = 5;

        foreach ($record->items as $index => $item) {
            $master = $item->item;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $master?->brand ?: $item->item_name);
            $sheet->setCellValue('C' . $row, $item->item_name);
            $sheet->setCellValue('D' . $row, $master?->model);
            $sheet->setCellValue('E' . $row, $item->item_location);
            $sheet->setCellValue('F' . $row, $master?->year);
            $sheet->setCellValue('G' . $row, $this->exportDescriptionText($item));

            $row++;
        }

        $this->styleExportTable($sheet, 'A4:G' . max(4, $row - 1));
        $this->autosizeColumns($sheet, range('A', 'G'));
    }

    private function buildLabInventoryExportSheet(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaris Lab');

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', strtoupper($record->title));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Periode: ' . $this->recordPeriodLabel($record));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'NAMA SUB KELOMPOK');
        $sheet->setCellValue('C4', 'IDENTIFIKASI / SPESIFIKASI');
        $sheet->setCellValue('D4', 'MERK');
        $sheet->setCellValue('E4', 'TYPE');
        $sheet->setCellValue('F4', 'TAHUN PEROLEHAN');
        $sheet->setCellValue('G4', 'JUMLAH BARANG');
        $sheet->setCellValue('H4', 'KONDISI BAIK');
        $sheet->setCellValue('I4', 'KONDISI RUSAK');
        $sheet->setCellValue('J4', 'NO PC');

        $row = 5;

        foreach ($record->items as $index => $item) {
            $master = $item->item;

            [$goodCondition, $brokenCondition] = $this->extractInventoryConditionFromDescription($item->description);

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->item_name);
            $sheet->setCellValue('C' . $row, $this->extractSpecificationFromDescription($item->description));
            $sheet->setCellValue('D' . $row, $master?->brand);
            $sheet->setCellValue('E' . $row, $master?->model);
            $sheet->setCellValue('F' . $row, $master?->year);
            $sheet->setCellValue('G' . $row, $master?->quantity);
            $sheet->setCellValue('H' . $row, $goodCondition);
            $sheet->setCellValue('I' . $row, $brokenCondition);
            $sheet->setCellValue('J' . $row, $item->item_identifier);

            $row++;
        }

        $this->styleExportTable($sheet, 'A4:J' . max(4, $row - 1));
        $this->autosizeColumns($sheet, range('A', 'J'));
    }

    private function buildLabCheckExportSheet(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pemeriksaan Lab');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', strtoupper($record->title));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Periode: ' . $this->recordPeriodLabel($record));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'A4' => 'NO',
            'B4' => 'NO PC',
            'C4' => 'OS',
            'D4' => 'APLIKASI',
            'E4' => 'MOUSE',
            'F4' => 'KEYBOARD',
            'G4' => 'MONITOR',
            'H4' => 'CPU',
            'I4' => 'KETERANGAN',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $row = 5;

        foreach ($record->items as $index => $item) {
            $components = $item->component_status ?? [];

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->item_identifier ?: $item->item_name);
            $sheet->setCellValue('C' . $row, $this->componentExportLabel($components['os'] ?? null));
            $sheet->setCellValue('D' . $row, $this->componentExportLabel($components['aplikasi'] ?? null));
            $sheet->setCellValue('E' . $row, $this->componentExportLabel($components['mouse'] ?? null));
            $sheet->setCellValue('F' . $row, $this->componentExportLabel($components['keyboard'] ?? null));
            $sheet->setCellValue('G' . $row, $this->componentExportLabel($components['monitor'] ?? null));
            $sheet->setCellValue('H' . $row, $this->componentExportLabel($components['cpu'] ?? null));
            $sheet->setCellValue('I' . $row, $item->description);

            $row++;
        }

        $this->styleExportTable($sheet, 'A4:I' . max(4, $row - 1));
        $this->autosizeColumns($sheet, range('A', 'I'));
    }

    private function buildGenericExportSheet(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Operasional');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', strtoupper($record->title));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'A3' => 'NO',
            'B3' => 'ITEM',
            'C3' => 'LOKASI',
            'D3' => 'KONDISI',
            'E3' => 'KETERANGAN',
            'F3' => 'TINDAKAN',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $row = 4;

        foreach ($record->items as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->item_name);
            $sheet->setCellValue('C' . $row, $item->item_location);
            $sheet->setCellValue('D' . $row, $item->condition_label);
            $sheet->setCellValue('E' . $row, $item->description);
            $sheet->setCellValue('F' . $row, $item->action_taken);

            $row++;
        }

        $this->styleExportTable($sheet, 'A3:F' . max(3, $row - 1));
        $this->autosizeColumns($sheet, range('A', 'F'));
    }

    private function styleExportTable($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $headerRange = preg_replace('/:\w+\d+$/', ':' . preg_replace('/\d+/', '', explode(':', $range)[1]) . preg_replace('/\D+/', '', explode(':', $range)[0]), $range);

        $firstCell = explode(':', $range)[0];
        preg_match('/^([A-Z]+)(\d+)$/', $firstCell, $matches);

        if ($matches) {
            $startRow = $matches[2];
            $endColumn = preg_replace('/\d+/', '', explode(':', $range)[1]);
            $headerRange = 'A' . $startRow . ':' . $endColumn . $startRow;

            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E8F0',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
        }
    }

    private function autosizeColumns($sheet, array $columns): void
    {
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function recordPeriodLabel(OperationalRecord $record): string
    {
        $months = $this->monthOptions();

        if ($record->period_month && $record->period_year) {
            return ($months[$record->period_month] ?? $record->period_month) . ' ' . $record->period_year;
        }

        return $record->record_date?->format('d M Y') ?? '-';
    }

    private function componentExportLabel(?string $value): string
    {
        return match ($value) {
            OperationalRecordItem::COMPONENT_DAMAGED => 'Rusak',
            OperationalRecordItem::COMPONENT_NOT_AVAILABLE => 'Tidak Ada',
            default => 'Baik',
        };
    }

    private function exportDescriptionText(OperationalRecordItem $item): ?string
    {
        $parts = [];

        if ($item->condition_status && $item->condition_status !== OperationalRecordItem::CONDITION_NORMAL) {
            $parts[] = 'Kondisi: ' . $item->condition_label;
        }

        if (filled($item->description)) {
            $parts[] = $item->description;
        }

        if (filled($item->action_taken)) {
            $parts[] = 'Tindakan: ' . $item->action_taken;
        }

        return filled($parts) ? implode("\n", $parts) : null;
    }

    private function extractSpecificationFromDescription(?string $description): ?string
    {
        if (blank($description)) {
            return null;
        }

        foreach (explode("\n", $description) as $line) {
            $line = trim($line);

            if (str_starts_with(strtolower($line), 'spesifikasi:')) {
                return trim(substr($line, strlen('spesifikasi:')));
            }
        }

        return $description;
    }

    private function extractInventoryConditionFromDescription(?string $description): array
    {
        $good = null;
        $broken = null;

        if (blank($description)) {
            return [$good, $broken];
        }

        foreach (explode("\n", $description) as $line) {
            $line = trim($line);
            $lower = strtolower($line);

            if (str_starts_with($lower, 'kondisi baik:')) {
                $good = trim(substr($line, strlen('kondisi baik:')));
            }

            if (str_starts_with($lower, 'kondisi rusak:')) {
                $broken = trim(substr($line, strlen('kondisi rusak:')));
            }
        }

        return [$good, $broken];
    }

    private function templatePathForRecord(OperationalRecord $record): ?string
    {
        $fileName = match ($record->category) {
            OperationalItem::CATEGORY_NETWORK => 'template_rekap_jaringan.xlsx',
            OperationalItem::CATEGORY_LAB_INVENTORY => 'template_inventaris_lab.xlsx',
            OperationalItem::CATEGORY_LAB_CHECK => 'template_pemeriksaan_lab.xlsx',
            default => null,
        };

        if (! $fileName) {
            return null;
        }

        return storage_path('app/templates/operations/' . $fileName);
    }

    private function normalizeSheetKey(?string $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['.', '-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    private function findSheetByLocation(Spreadsheet $spreadsheet, ?string $location): ?Worksheet
    {
        $target = $this->normalizeSheetKey($location);

        if (blank($target)) {
            return $spreadsheet->getSheet(0);
        }

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            if ($this->normalizeSheetKey($sheet->getTitle()) === $target) {
                return $sheet;
            }
        }

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            if (
                str_contains($this->normalizeSheetKey($sheet->getTitle()), $target)
                || str_contains($target, $this->normalizeSheetKey($sheet->getTitle()))
            ) {
                return $sheet;
            }
        }

        return null;
    }

    private function getOrCreateSheetForLocation(Spreadsheet $spreadsheet, ?string $location): Worksheet
    {
        $sheet = $this->findSheetByLocation($spreadsheet, $location);

        if ($sheet) {
            return $sheet;
        }

        $templateSheet = $spreadsheet->getSheet(0);
        $newSheet = clone $templateSheet;

        $title = filled($location) ? substr($location, 0, 31) : 'Sheet Baru';
        $title = $this->uniqueSheetTitle($spreadsheet, $title);

        $newSheet->setTitle($title);
        $spreadsheet->addSheet($newSheet);

        return $newSheet;
    }

    private function uniqueSheetTitle(Spreadsheet $spreadsheet, string $title): string
    {
        $title = trim($title) ?: 'Sheet Baru';
        $title = substr($title, 0, 31);

        $existing = collect($spreadsheet->getSheetNames())
            ->map(fn ($name) => strtolower($name))
            ->toArray();

        if (! in_array(strtolower($title), $existing, true)) {
            return $title;
        }

        $base = substr($title, 0, 25);
        $counter = 2;

        while (in_array(strtolower($base . ' ' . $counter), $existing, true)) {
            $counter++;
        }

        return $base . ' ' . $counter;
    }

    private function clearTemplateDataRows(Worksheet $sheet, int $startRow, int $endColumnIndex, int $defaultRows = 80): void
    {
        $highestRow = max($sheet->getHighestRow(), $startRow + $defaultRows);

        for ($row = $startRow; $row <= $highestRow; $row++) {
            for ($column = 1; $column <= $endColumnIndex; $column++) {
                $sheet->setCellValueByColumnAndRow($column, $row, null);
            }
        }
    }

    private function prepareRowsFromTemplate(Worksheet $sheet, int $startRow, int $neededRows, int $styleColumnCount): void
    {
        if ($neededRows <= 1) {
            return;
        }

        $sheet->insertNewRowBefore($startRow + 1, $neededRows - 1);

        for ($row = $startRow + 1; $row < $startRow + $neededRows; $row++) {
            for ($column = 1; $column <= $styleColumnCount; $column++) {
                $sourceCell = $sheet->getCellByColumnAndRow($column, $startRow);
                $targetCell = $sheet->getCellByColumnAndRow($column, $row);

                $sheet->duplicateStyle(
                    $sheet->getStyle($sourceCell->getCoordinate()),
                    $targetCell->getCoordinate()
                );
            }

            $sheet->getRowDimension($row)->setRowHeight(
                $sheet->getRowDimension($startRow)->getRowHeight()
            );
        }
    }

    private function setCommonTemplateHeader(Worksheet $sheet, OperationalRecord $record): void
    {
        $replacements = [
            '{{judul}}' => strtoupper($record->title),
            '{{periode}}' => $this->recordPeriodLabel($record),
            '{{unit}}' => $record->unit?->name ?? '',
            '{{tanggal}}' => $record->record_date?->format('d/m/Y') ?? '',
        ];

        $highestRow = min($sheet->getHighestRow(), 15);
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {
            foreach (range('A', $highestColumn) as $column) {
                $cell = $column . $row;
                $value = $sheet->getCell($cell)->getValue();

                if (! is_string($value)) {
                    continue;
                }

                foreach ($replacements as $search => $replace) {
                    if (str_contains($value, $search)) {
                        $sheet->setCellValue($cell, str_replace($search, $replace, $value));
                    }
                }
            }
        }
    }

    private function fillNetworkTemplate(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $itemsByLocation = $record->items
            ->groupBy(fn ($item) => $item->item_location ?: 'Tanpa Lokasi');

        foreach ($itemsByLocation as $location => $items) {
            $sheet = $this->getOrCreateSheetForLocation($spreadsheet, $location);

            $this->setCommonTemplateHeader($sheet, $record);

            // Template resmi jaringan:
            // Row 12-13 = header tabel
            // Row 14    = awal data
            $startRow = 14;
            $maxColumn = $this->isDormitoryNetworkSheet($sheet) ? 9 : 10;

            $footerRow = $this->findNetworkTemplateFooterRow($sheet, $startRow);

            if (! $footerRow) {
                $footerRow = $sheet->getHighestRow() + 1;
            }

            $availableRows = max(0, $footerRow - $startRow);
            $neededRows = $items->count();

            if ($neededRows > $availableRows) {
                $rowsToInsert = $neededRows - $availableRows;

                // Insert tepat sebelum footer, jadi footer/tanda tangan tetap aman dan turun rapi.
                $sheet->insertNewRowBefore($footerRow, $rowsToInsert);

                // Copy style dari baris data terakhir sebelum footer.
                $styleSourceRow = max($startRow, $footerRow - 1);

                for ($row = $footerRow; $row < $footerRow + $rowsToInsert; $row++) {
                    $this->copyRowStyle($sheet, $styleSourceRow, $row, $maxColumn);
                }

                $footerRow += $rowsToInsert;
            }

            $this->clearNetworkDataArea($sheet, $startRow, $footerRow - 1, $maxColumn);

            $row = $startRow;

            foreach ($items->values() as $index => $item) {
                $master = $item->item;

                if ($this->isDormitoryNetworkSheet($sheet)) {
                    $this->fillDormitoryNetworkRow($sheet, $row, $index + 1, $item, $master);
                } else {
                    $this->fillStipNetworkRow($sheet, $row, $index + 1, $item, $master);
                }

                $row++;
            }

            $this->fillNetworkTemplatePeriod($sheet, $record);
            $this->fillOperationalSignatureArea($sheet, $record);
        }

        $spreadsheet->setActiveSheetIndex(0);
    }

    private function fillLabInventoryTemplate(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $itemsByLocation = $record->items
            ->groupBy(fn ($item) => $item->item_location ?: 'Tanpa Lokasi');

        foreach ($itemsByLocation as $location => $items) {
            $sheet = $this->getOrCreateSheetForLocation($spreadsheet, $location);

            $this->setCommonTemplateHeader($sheet, $record);

            $startRow = 11;
            $this->clearTemplateDataRows($sheet, $startRow, 10);
            $this->prepareRowsFromTemplate($sheet, $startRow, $items->count(), 10);

            $row = $startRow;

            foreach ($items->values() as $index => $item) {
                $master = $item->item;
                [$goodCondition, $brokenCondition] = $this->extractInventoryConditionFromDescription($item->description);

                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->item_name);
                $sheet->setCellValue('C' . $row, $this->extractSpecificationFromDescription($item->description));
                $sheet->setCellValue('D' . $row, $master?->brand);
                $sheet->setCellValue('E' . $row, $master?->model);
                $sheet->setCellValue('F' . $row, $master?->year);
                $sheet->setCellValue('G' . $row, $master?->quantity);
                $sheet->setCellValue('H' . $row, $goodCondition);
                $sheet->setCellValue('I' . $row, $brokenCondition);
                $sheet->setCellValue('J' . $row, $item->item_identifier);

                $row++;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
    }

    private function fillLabCheckTemplate(Spreadsheet $spreadsheet, OperationalRecord $record): void
    {
        $itemsByLocation = $record->items
            ->groupBy(fn ($item) => $item->item_location ?: 'Tanpa Lokasi');

        foreach ($itemsByLocation as $location => $items) {
            $sheet = $this->getOrCreateSheetForLocation($spreadsheet, $location);

            $this->setCommonTemplateHeader($sheet, $record);

            $startRow = 5;
            $this->clearTemplateDataRows($sheet, $startRow, 9);
            $this->prepareRowsFromTemplate($sheet, $startRow, $items->count(), 9);

            $row = $startRow;

            foreach ($items->values() as $index => $item) {
                $components = $item->component_status ?? [];

                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->item_identifier ?: $item->item_name);
                $sheet->setCellValue('C' . $row, $this->componentExportLabel($components['os'] ?? null));
                $sheet->setCellValue('D' . $row, $this->componentExportLabel($components['aplikasi'] ?? null));
                $sheet->setCellValue('E' . $row, $this->componentExportLabel($components['mouse'] ?? null));
                $sheet->setCellValue('F' . $row, $this->componentExportLabel($components['keyboard'] ?? null));
                $sheet->setCellValue('G' . $row, $this->componentExportLabel($components['monitor'] ?? null));
                $sheet->setCellValue('H' . $row, $this->componentExportLabel($components['cpu'] ?? null));
                $sheet->setCellValue('I' . $row, $item->description);

                $row++;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
    }

    private function safeExportFileName(string $fileName): string
    {
        $fileName = preg_replace('/[\\\\\/:*?"<>|]+/', '-', $fileName);
        $fileName = preg_replace('/\s+/', ' ', $fileName);

        return trim($fileName);
    }

    private function isDormitoryNetworkSheet(Worksheet $sheet): bool
    {
        return str_contains($this->normalizeSheetKey($sheet->getTitle()), 'dormi');
    }

    private function findNetworkTemplateFooterRow(Worksheet $sheet, int $startRow): ?int
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rowText = '';

            for ($column = 1; $column <= 12; $column++) {
                $value = $sheet->getCellByColumnAndRow($column, $row)->getValue();

                if (filled($value)) {
                    $rowText .= ' ' . strtolower(trim((string) $value));
                }
            }

            if (
                str_contains($rowText, 'keterangan :')
                || str_contains($rowText, 'mengetahui')
                || str_contains($rowText, 'kepala')
                || str_contains($rowText, 'teknisi')
                || str_contains($rowText, 'petugas')
                || str_contains($rowText, 'tanggal')
            ) {
                return $row;
            }
        }

        return null;
    }

    private function clearNetworkDataArea(Worksheet $sheet, int $startRow, int $endRow, int $maxColumn): void
    {
        if ($endRow < $startRow) {
            return;
        }

        for ($row = $startRow; $row <= $endRow; $row++) {
            for ($column = 1; $column <= $maxColumn; $column++) {
                $sheet->setCellValueByColumnAndRow($column, $row, null);
            }
        }
    }

    private function copyRowStyle(Worksheet $sheet, int $sourceRow, int $targetRow, int $maxColumn): void
    {
        for ($column = 1; $column <= $maxColumn; $column++) {
            $sourceCell = $sheet->getCellByColumnAndRow($column, $sourceRow);
            $targetCell = $sheet->getCellByColumnAndRow($column, $targetRow);

            $sheet->duplicateStyle(
                $sheet->getStyle($sourceCell->getCoordinate()),
                $targetCell->getCoordinate()
            );
        }

        $sheet->getRowDimension($targetRow)->setRowHeight(
            $sheet->getRowDimension($sourceRow)->getRowHeight()
        );
    }

    private function fillDormitoryNetworkRow(Worksheet $sheet, int $row, int $number, OperationalRecordItem $item, ?OperationalItem $master): void
    {
        // Template dormi:
        // A     NO
        // B     MERK / ACCESS POINT
        // C-D   NAMA PERANGKAT
        // E     MODEL
        // F     LOKASI
        // G-H   TAHUN
        // I     KETERANGAN

        $sheet->setCellValue('A' . $row, $number);
        $sheet->setCellValue('B' . $row, $master?->brand ?: $item->item_name);
        $sheet->setCellValue('C' . $row, $item->item_name);
        $sheet->setCellValue('E' . $row, $master?->model);
        $sheet->setCellValue('F' . $row, $item->item_location);
        $sheet->setCellValue('G' . $row, $master?->year);
        $sheet->setCellValue('I' . $row, $this->exportDescriptionText($item));
    }

    private function fillStipNetworkRow(Worksheet $sheet, int $row, int $number, OperationalRecordItem $item, ?OperationalItem $master): void
    {
        // Template STIP/STIP (2):
        // A NO
        // B MERK / ACCESS POINT
        // C SERIES/TIPE
        // D RUANG
        // E NAMA GEDUNG
        // F TAHUN
        // G JUMLAH
        // H KONDISI
        // I KEPEMILIKAN
        // J KETERANGAN

        $sheet->setCellValue('A' . $row, $number);
        $sheet->setCellValue('B' . $row, $master?->brand ?: $item->item_name);
        $sheet->setCellValue('C' . $row, $master?->model ?: $item->item_name);
        $sheet->setCellValue('D' . $row, $item->item_location);
        $sheet->setCellValue('E' . $row, $item->item_location);
        $sheet->setCellValue('F' . $row, $master?->year);
        $sheet->setCellValue('G' . $row, $master?->quantity ?: 1);
        $sheet->setCellValue('H' . $row, $item->condition_label);
        $sheet->setCellValue('I' . $row, null);
        $sheet->setCellValue('J' . $row, $this->exportDescriptionText($item));
    }

    private function technicianOptionsForUnit(?int $unitId)
    {
        if (! $unitId) {
            return collect();
        }

        return Employee::query()
            ->where('unit_id', $unitId)
            ->orderBy('name')
            ->get();
    }

    private function resolveDefaultTechnicianEmployeeId(?int $unitId, ?string $category): ?int
    {
        if (! $unitId || ! $category) {
            return null;
        }

        $keywords = $this->technicianDutyKeywords($category);

        if (empty($keywords)) {
            return null;
        }

        $query = Employee::query()
            ->where('unit_id', $unitId)
            ->whereHas('duties', function ($dutyQuery) use ($keywords) {
                $dutyQuery->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('name', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%');
                    }
                });
            })
            ->orderBy('name');

        return $query->value('id');
    }

    private function technicianDutyKeywords(string $category): array
    {
        return match ($category) {
            OperationalItem::CATEGORY_NETWORK => [
                'jaringan',
                'internet',
                'access point',
                'wifi',
                'switch',
                'router',
                'infrastruktur',
            ],

            OperationalItem::CATEGORY_LAB_INVENTORY => [
                'laboratorium',
                'lab',
                'inventaris',
                'perangkat komputer',
                'komputer',
                'rpk',
            ],

            OperationalItem::CATEGORY_LAB_CHECK => [
                'laboratorium',
                'lab',
                'komputer',
                'pemeriksaan',
                'perawatan',
                'maintenance',
                'rpk',
            ],

            default => [],
        };
    }

    private function fillNetworkTemplatePeriod(Worksheet $sheet, OperationalRecord $record): void
    {
        // Di template jaringan, label "Periode Bulan" ada di row 10.
        // Umumnya titik dua ada di C10, value aman ditaruh di D10.
        $sheet->setCellValue('D10', $this->recordPeriodLabel($record));
    }

    private function resolveKanitEmployeeForRecord(OperationalRecord $record): ?Employee
    {
        if (! $record->unit_id) {
            return null;
        }

        return User::query()
            ->with('employee')
            ->get()
            ->first(function ($user) use ($record) {
                return method_exists($user, 'isKanit')
                    && $user->isKanit()
                    && $user->employee
                    && (int) $user->employee->unit_id === (int) $record->unit_id;
            })
            ?->employee;
    }

    private function resolveTechnicianEmployeeForRecord(OperationalRecord $record): ?Employee
    {
        if ($record->technician) {
            return $record->technician;
        }

        return $record->createdBy?->employee;
    }

    private function fillOperationalSignatureArea(Worksheet $sheet, OperationalRecord $record): void
    {
        $kanit = $this->resolveKanitEmployeeForRecord($record);
        $technician = $this->resolveTechnicianEmployeeForRecord($record);

        $signatureRow = $this->findOperationalSignatureRow($sheet);

        if (! $signatureRow) {
            return;
        }

        $dateRow = max(1, $signatureRow - 1);
        $imageRow = $signatureRow + 1;
        $nameRow = $signatureRow + 5;
        $nipRow = $signatureRow + 6;

        // Kiri: Kanit
        if ($kanit) {
            $this->insertEmployeeSignatureImage($sheet, $kanit, 'B' . $imageRow);
            $sheet->setCellValue('B' . $nameRow, $kanit->name);
            $sheet->setCellValue('B' . $nipRow, 'NIP. ' . ($kanit->nip ?? '-'));
        }

        // Kanan: Teknisi
        if ($technician) {
            $sheet->setCellValue('I' . $dateRow, $this->operationalSignatureDateLabel($record));

            $this->insertEmployeeSignatureImage($sheet, $technician, 'I' . $imageRow);
            $sheet->setCellValue('I' . $nameRow, $technician->name);
            $sheet->setCellValue('I' . $nipRow, 'NIP. ' . ($technician->nip ?? '-'));
        }
    }

    private function findOperationalSignatureRow(Worksheet $sheet): ?int
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowText = '';

            for ($column = 1; $column <= 12; $column++) {
                $value = $sheet->getCellByColumnAndRow($column, $row)->getValue();

                if (filled($value)) {
                    $rowText .= ' ' . strtolower(trim((string) $value));
                }
            }

            if (
                str_contains($rowText, 'mengetahui')
                || str_contains($rowText, 'kepala')
                || str_contains($rowText, 'teknisi')
                || str_contains($rowText, 'petugas')
            ) {
                return $row;
            }
        }

        return null;
    }

    private function operationalSignatureDateLabel(OperationalRecord $record): string
    {
        $date = $record->submitted_at
            ?? $record->record_date
            ?? $record->created_at
            ?? now();

        return 'Jakarta, ' . $date->format('d/m/Y');
    }

    private function employeeSignaturePath(?Employee $employee): ?string
    {
        if (! $employee || blank($employee->signature_path)) {
            return null;
        }

        $paths = [
            storage_path('app/' . $employee->signature_path),
            storage_path('app/private/' . $employee->signature_path),
            public_path('storage/' . $employee->signature_path),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function insertEmployeeSignatureImage(Worksheet $sheet, ?Employee $employee, string $coordinate): void
    {
        $path = $this->employeeSignaturePath($employee);

        if (! $path) {
            return;
        }

        $drawing = new Drawing();
        $drawing->setName('Tanda Tangan');
        $drawing->setDescription('Tanda Tangan ' . $employee->name);
        $drawing->setPath($path);
        $drawing->setCoordinates($coordinate);
        $drawing->setHeight(55);
        $drawing->setOffsetX(20);
        $drawing->setOffsetY(4);
        $drawing->setWorksheet($sheet);
    }
}