<?php

namespace App\Livewire\Reports;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\JobDuty;
use App\Models\DutyDelegation;
use App\Models\ReportTemplate;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\ActivityLogger;

class CreateDailyReport extends Component
{
    use WithFileUploads;

    public array $form = [
        'report_date' => '',
        'duty_id' => '',
        'server_id' => '',
        'application_id' => '',
        'template_id' => '',
        'title' => '',
        'description' => '',
        'notes' => '',
    ];

    public bool $missingEmployee = false;
    public bool $missingUnit = false;

    public array $photos = [];
    public array $newPhotos = [];
    public int $photoInputKey = 0;

    public $templates = [];
    public $duties = [];

    public $personalDuties = [];
    public $delegatedDuties = [];
    public $selectedDutyType = 'personal';
    public $selectedDelegationId = null;
    public $selected_duty = null;

    public function mount()
    {
        $user = Auth::user();

        if (! $user->employee_id || ! $user->employee) {
            $this->missingEmployee = true;
            $this->duties = collect();
            return;
        }

        if (! $user->employee->unit_id) {
            $this->missingUnit = true;
            $this->duties = collect();
            return;
        }

        $this->form['report_date'] = now()->format('Y-m-d');

        $this->loadEmployeeDuties();

        $this->templates = ReportTemplate::query()
            ->where('is_active', 1)
            ->orderBy('title')
            ->get();

        $this->personalDuties = [];
        $this->delegatedDuties = [];

        $this->loadAvailableDuties();
    }

    private function loadEmployeeDuties(): void
    {
        $employee = Auth::user()?->employee;

        if (! $employee) {
            $this->duties = collect();
            return;
        }

        $this->duties = $employee->duties()
            ->with(['unit', 'classification', 'server', 'application'])
            ->orderBy('name')
            ->get();
    }

    public function updatedFormServerId($value): void
    {
        $this->form['application_id'] = '';
    }

    /* public function updatedFormDutyId($value): void
    {
        if (empty($value)) {
            return;
        }

        $employee = Auth::user()?->employee;

        if (! $employee) {
            $this->form['duty_id'] = '';
            $this->addError('form.duty_id', 'Data pegawai tidak ditemukan pada akun ini.');
            return;
        }

        $isDutyAssignedToEmployee = $employee->duties()
            ->where('duties.id', $value)
            ->exists();

        if (! $isDutyAssignedToEmployee) {
            $this->form['duty_id'] = '';
            $this->addError('form.duty_id', 'Tupoksi tidak tersedia untuk pegawai ini.');
            return;
        }
    } */

    public function updatedFormTemplateId($value): void
    {
        if (empty($value)) {
            return;
        }

        $template = ReportTemplate::query()->find($value);

        if (! $template) {
            return;
        }

        $replacements = $this->getTemplateReplacements();

        if (! empty($template->title)) {
            $this->form['title'] = $this->replaceTemplatePlaceholders($template->title, $replacements);
        }

        if (! empty($template->description_template)) {
            $this->form['description'] = $this->replaceTemplatePlaceholders($template->description_template, $replacements);
        }

        if (! empty($template->result_template)) {
            $this->form['notes'] = $this->replaceTemplatePlaceholders($template->result_template, $replacements);
        }
    }

    public function updatedNewPhotos()
    {
        $this->validate([
            'newPhotos' => ['nullable', 'array'],
            'newPhotos.*' => ['image', 'max:5120'],
        ], [
            'newPhotos.*.image' => 'File harus berupa gambar.',
            'newPhotos.*.max' => 'Ukuran foto maksimal 5 MB per file.',
        ]);

        foreach ($this->newPhotos as $photo) {
            if (count($this->photos) >= 5) {
                $this->addError('photos', 'Maksimal upload 5 foto.');
                break;
            }

            $this->photos[] = $photo;
        }

        $this->newPhotos = [];
        $this->photoInputKey++;
    }

    public function removePhoto($index): void
    {
        unset($this->photos[$index]);

        $this->photos = array_values($this->photos);
    }

    public function cloneLastReport(): void
    {
        $user = Auth::user();

        if (! $user->employee_id) {
            session()->flash('error', 'Akun belum terhubung dengan data pegawai.');
            return;
        }

        $lastReport = DailyReport::query()
            ->where('employee_id', $user->employee_id)
            ->latest('report_date')
            ->latest('id')
            ->first();

        if (! $lastReport) {
            session()->flash('error', 'Belum ada laporan sebelumnya untuk diclone.');
            return;
        }

        $employee = $user->employee;

        $isDutyAssignedToEmployee = $employee?->duties()
            ->where('duties.id', $lastReport->duty_id)
            ->exists();

        if (! $isDutyAssignedToEmployee) {
            session()->flash('error', 'Laporan terakhir menggunakan tupoksi yang belum ditugaskan ke akun Anda.');
            return;
        }

        $this->selected_duty = $lastReport->duty_id ? 'personal:' . $lastReport->duty_id : null;
        $this->form['server_id'] = $lastReport->server_id ? (string) $lastReport->server_id : '';
        $this->form['application_id'] = $lastReport->application_id ? (string) $lastReport->application_id : '';
        $this->form['title'] = $lastReport->title ?? '';
        $this->form['description'] = $lastReport->description ?? '';
        $this->form['notes'] = $lastReport->notes ?? '';

        session()->flash('success', 'Laporan terakhir berhasil diclone. Silakan sesuaikan sebelum disimpan.');
    }

    public function save()
    {
        $user = Auth::user();

        if (! $user->employee_id) {
            abort(403, 'Akun belum terhubung dengan data pegawai.');
        }

        if (! $user->employee || ! $user->employee->unit_id) {
            abort(403, 'Data pegawai belum memiliki unit kerja.');
        }

       $validated = $this->validate([
            'form.report_date' => ['required', 'date'],
            'selected_duty' => ['required', 'string'],
            'form.server_id' => ['nullable', 'exists:servers,id'],
            'form.application_id' => ['nullable', 'exists:applications,id'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.description' => ['required', 'string'],
            'form.notes' => ['nullable', 'string'],

            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:5120'],
        ], [
            'form.report_date.required' => 'Tanggal laporan wajib diisi.',
            'form.report_date.date' => 'Format tanggal laporan tidak valid.',
            'selected_duty.required' => 'Tupoksi wajib dipilih.',
            'form.server_id.exists' => 'Server tidak valid.',
            'form.application_id.exists' => 'Aplikasi tidak valid.',
            'form.title.required' => 'Judul kegiatan wajib diisi.',
            'form.title.max' => 'Judul kegiatan maksimal 255 karakter.',
            'form.description.required' => 'Deskripsi kegiatan wajib diisi.',
            'photos.max' => 'Maksimal upload 5 foto.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 5 MB per file.',
        ]);

        $resolvedDuty = $this->resolveSelectedDutyForSave();

        if (!$resolvedDuty) {
            $this->addError('selected_duty', 'Tupoksi tidak valid atau delegasi tidak aktif pada tanggal laporan.');
            return;
        }

        $employee = $user->employee;
        $form = $validated['form'];

        if (! empty($form['application_id']) && ! empty($form['server_id'])) {
            $applicationBelongsToServer = Application::query()
                ->where('id', $form['application_id'])
                ->where('server_id', $form['server_id'])
                ->exists();

            if (! $applicationBelongsToServer) {
                $this->addError('form.application_id', 'Aplikasi tidak sesuai dengan server yang dipilih.');
                return;
            }
        }

        $report = DailyReport::create([
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'unit_id' => $employee->unit_id,

            'duty_id' => $resolvedDuty['duty_id'],
            'is_delegated' => $resolvedDuty['is_delegated'],
            'delegation_id' => $resolvedDuty['delegation_id'],
            'duty_owner_employee_id' => $resolvedDuty['duty_owner_employee_id'],
            'reported_by_employee_id' => $resolvedDuty['reported_by_employee_id'],

            'server_id' => $form['server_id'] ?: null,
            'application_id' => $form['application_id'] ?: null,
            'report_date' => $form['report_date'],
            'title' => $form['title'],
            'description' => $form['description'],
            'notes' => $form['notes'] ?: null,
            'status' => 'submitted',
        ]);

        $this->storeCompressedPhotos($report);

        $freshReport = $report->fresh()->toArray();

        $freshReport['photo_count'] = DailyReportPhoto::query()
            ->where('daily_report_id', $report->id)
            ->count();

        $freshReport['photo_paths'] = DailyReportPhoto::query()
            ->where('daily_report_id', $report->id)
            ->orderBy('sort_order')
            ->pluck('file_path')
            ->toArray();

        ActivityLogger::log(
            module: 'daily_report',
            action: 'create',
            description: $report->is_delegated
                ? 'Membuat laporan kerja harian delegasi'
                : 'Membuat laporan kerja harian',
            subject: $report,
            newValues: $freshReport
        );

        session()->flash('success', 'Laporan kerja harian berhasil disimpan.');

        return redirect()->route('pegawai.reports.index');
    }

    private function storeCompressedPhotos(DailyReport $report): void
    {
        if (empty($this->photos)) {
            return;
        }

        $manager = new ImageManager(new Driver());

        foreach ($this->photos as $index => $photo) {
            $directory = 'daily-reports/' . now()->format('Y/m');
            $filename = 'report-' . $report->id . '-' . uniqid() . '.jpg';
            $path = $directory . '/' . $filename;

            $image = $manager->read($photo->getRealPath())
                ->scaleDown(width: 1280);

            $encodedImage = $image->toJpeg(75);

            Storage::disk('public')->put($path, (string) $encodedImage);

            DailyReportPhoto::create([
                'daily_report_id' => $report->id,
                'file_path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'compressed_size' => Storage::disk('public')->size($path),
                'mime_type' => 'image/jpeg',
                'sort_order' => $index,
            ]);
        }
    }

    private function replaceTemplatePlaceholders(?string $text, array $replacements): string
    {
        if (empty($text)) {
            return '';
        }

        return strtr($text, $replacements);
    }

    private function getTemplateReplacements(): array
    {
        $application = ! empty($this->form['application_id'])
            ? Application::query()->find($this->form['application_id'])
            : null;

        $server = ! empty($this->form['server_id'])
            ? Server::query()->find($this->form['server_id'])
            : null;

        $duty = null;

        $selected = $this->parseSelectedDuty();

        if ($selected['type'] === 'personal' && $selected['id']) {
            $employee = Auth::user()?->employee;

            $duty = $employee?->duties()
                ->where('duties.id', $selected['id'])
                ->first();
        }

        if ($selected['type'] === 'delegation' && $selected['id']) {
            $delegation = DutyDelegation::query()
                ->with('duty')
                ->where('id', $selected['id'])
                ->first();

            $duty = $delegation?->duty;
        }

        return [
            '{{application_name}}' => $application?->name ?? '-',
            '{{server_name}}' => $server?->name ?? '-',
            '{{duty_name}}' => $duty?->name ?? '-',
            '{{report_date}}' => ! empty($this->form['report_date'])
                ? Carbon::parse($this->form['report_date'])->translatedFormat('d F Y')
                : '-',
        ];
    }

    public function render()
    {
        $applications = collect();

        if (! empty($this->form['server_id'])) {
            $applications = Application::query()
                ->where('server_id', $this->form['server_id'])
                ->orderBy('name')
                ->get();
        }

        return view('livewire.reports.create-daily-report', [
            'duties' => $this->duties,
            'servers' => Server::query()->orderBy('name')->get(),
            'applications' => $applications,
            'templates' => $this->templates,
        ])->layout('layouts.app');
    }

    private function loadAvailableDuties(): void
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            $this->personalDuties = [];
            $this->delegatedDuties = [];
            return;
        }

        $reportDate = $this->getCurrentReportDate();

        $personalDutyIds = DB::table('employee_duty')
            ->where('employee_id', $employee->id)
            ->pluck('duty_id')
            ->toArray();

        $this->personalDuties = JobDuty::query()
            ->with(['classification', 'server', 'application'])
            ->whereIn('id', $personalDutyIds)
            ->orderBy('name')
            ->get()
            ->map(fn ($duty) => [
                'id' => $duty->id,
                'name' => $duty->name,
                'classification_name' => $duty->classification?->name,
                'object_type_label' => $duty->object_type_label,
                'work_object_label' => $duty->work_object_label,
            ])
            ->toArray();

        $this->delegatedDuties = DutyDelegation::query()
            ->with([
                'duty.classification',
                'duty.server',
                'duty.application',
                'ownerEmployee:id,name',
            ])
            ->where('delegate_employee_id', $employee->id)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $reportDate)
            ->where(function ($query) use ($reportDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $reportDate);
            })
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(fn ($delegation) => [
                'id' => $delegation->id,
                'duty_id' => $delegation->duty_id,
                'duty_name' => $delegation->duty?->name ?? '-',
                'owner_employee_id' => $delegation->owner_employee_id,
                'owner_name' => $delegation->ownerEmployee?->name ?? '-',
                'start_date' => optional($delegation->start_date)->format('Y-m-d'),
                'end_date' => optional($delegation->end_date)->format('Y-m-d'),
                'classification_name' => $delegation->duty?->classification?->name,
                'object_type_label' => $delegation->duty?->object_type_label ?? '-',
                'work_object_label' => $delegation->duty?->work_object_label ?? '-',
            ])
            ->toArray();
    }

    /* public function updatedReportDate()
    {
        $this->duty_id = null;
        $this->selectedDutyType = 'personal';
        $this->selectedDelegationId = null;

        $this->loadAvailableDuties();
    } */

    private function parseSelectedDuty(): array
    {
        if (!$this->selected_duty || !str_contains($this->selected_duty, ':')) {
            return [
                'type' => null,
                'id' => null,
            ];
        }

        [$type, $id] = explode(':', $this->selected_duty, 2);

        return [
            'type' => $type,
            'id' => (int) $id,
        ];
    }

    public function getSelectedDutyInfoProperty(): ?array
    {
        $selected = $this->parseSelectedDuty();

        if (! $selected['type'] || ! $selected['id']) {
            return null;
        }

        if ($selected['type'] === 'personal') {
            $duty = JobDuty::query()
                ->with(['classification', 'server', 'application'])
                ->find($selected['id']);

            if (! $duty) {
                return null;
            }

            return [
                'name' => $duty->name,
                'source' => 'Tupoksi Pribadi',
                'owner_name' => null,
                'classification_name' => $duty->classification?->name ?? 'Tanpa klasifikasi',
                'object_type_label' => $duty->object_type_label,
                'work_object_label' => $duty->work_object_label,
            ];
        }

        if ($selected['type'] === 'delegation') {
            $delegation = DutyDelegation::query()
                ->with([
                    'duty.classification',
                    'duty.server',
                    'duty.application',
                    'ownerEmployee:id,name',
                ])
                ->find($selected['id']);

            if (! $delegation || ! $delegation->duty) {
                return null;
            }

            return [
                'name' => $delegation->duty->name,
                'source' => 'Tupoksi Delegasi',
                'owner_name' => $delegation->ownerEmployee?->name,
                'classification_name' => $delegation->duty->classification?->name ?? 'Tanpa klasifikasi',
                'object_type_label' => $delegation->duty->object_type_label,
                'work_object_label' => $delegation->duty->work_object_label,
            ];
        }

        return null;
    }

    private function getCurrentReportDate(): string
    {
        if (!empty($this->form['report_date'])) {
            return $this->form['report_date'];
        }

        return now()->toDateString();
    }

    private function resolveSelectedDutyForSave(): ?array
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return null;
        }

        $selected = $this->parseSelectedDuty();

        if (!$selected['type'] || !$selected['id']) {
            return null;
        }

        $reportDate = $this->getCurrentReportDate();

        if ($selected['type'] === 'personal') {
            $hasDuty = DB::table('employee_duty')
                ->where('employee_id', $employee->id)
                ->where('duty_id', $selected['id'])
                ->exists();

            if (!$hasDuty) {
                return null;
            }

            return [
                'duty_id' => $selected['id'],
                'is_delegated' => false,
                'delegation_id' => null,
                'duty_owner_employee_id' => $employee->id,
                'reported_by_employee_id' => $employee->id,
            ];
        }

        if ($selected['type'] === 'delegation') {
            $delegation = DutyDelegation::query()
                ->where('id', $selected['id'])
                ->where('delegate_employee_id', $employee->id)
                ->where('is_active', true)
                ->whereDate('start_date', '<=', $reportDate)
                ->where(function ($query) use ($reportDate) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $reportDate);
                })
                ->first();

            if (!$delegation) {
                return null;
            }

            return [
                'duty_id' => $delegation->duty_id,
                'is_delegated' => true,
                'delegation_id' => $delegation->id,
                'duty_owner_employee_id' => $delegation->owner_employee_id,
                'reported_by_employee_id' => $employee->id,
            ];
        }

        return null;
    }

    public function updated($property, $value): void
    {
        if (in_array($property, ['report_date', 'reportDate', 'date'])) {
            $this->duty_id = null;
            $this->loadAvailableDuties();
        }
    }

    public function updatedFormReportDate(): void
    {
        $this->selected_duty = null;
        $this->loadAvailableDuties();
    }
}