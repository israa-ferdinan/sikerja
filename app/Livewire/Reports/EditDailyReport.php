<?php

namespace App\Livewire\Reports;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\Duty;
use App\Models\Server;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditDailyReport extends Component
{
    use WithFileUploads;

    public DailyReport $report;

    public $report_date;
    public $duty_id;
    public $server_id;
    public $application_id;
    public $title;
    public $description;
    public $notes;

    public $duties = [];
    public $servers = [];
    public $applications = [];

    public $photos = [];
    public $newPhotos = [];
    public $photoInputKey = 0;

    public function mount(DailyReport $report)
    {
        $user = auth()->user();

        if (! $user->employee_id || $report->employee_id !== $user->employee_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        $this->report = $report->load('photos');

        $this->report_date = $report->report_date?->format('Y-m-d');
        $this->duty_id = $report->duty_id;
        $this->server_id = $report->server_id;
        $this->application_id = $report->application_id;
        $this->title = $report->title;
        $this->description = $report->description;
        $this->notes = $report->notes;

        $employeeDutyIds = DB::table('employee_duty')
            ->where('employee_id', $user->employee_id)
            ->pluck('duty_id')
            ->toArray();

        if ($report->duty_id && ! in_array($report->duty_id, $employeeDutyIds, true)) {
            $employeeDutyIds[] = $report->duty_id;
        }

        $this->duties = Duty::query()
            ->whereIn('id', $employeeDutyIds)
            ->orderBy('name')
            ->get();

        $this->servers = Server::orderBy('name')->get();

        $this->applications = $this->server_id
            ? Application::where('server_id', $this->server_id)->orderBy('name')->get()
            : collect();
    }

    public function updatedServerId()
    {
        $this->application_id = null;

        if (! $this->shouldShowServerField) {
            $this->server_id = null;
            $this->applications = collect();
            return;
        }

        $this->applications = ($this->shouldShowApplicationField && $this->server_id)
            ? Application::where('server_id', $this->server_id)->orderBy('name')->get()
            : collect();
    }

    public function updatedDutyId(): void
    {
        $this->server_id = null;
        $this->application_id = null;
        $this->applications = collect();

        $this->resetErrorBag([
            'server_id',
            'application_id',
        ]);
    }

    public function getSelectedDutyObjectTypeProperty(): ?string
    {
        if (! $this->duty_id) {
            return null;
        }

        return Duty::query()
            ->whereKey($this->duty_id)
            ->value('object_type');
    }

    public function getShouldShowServerFieldProperty(): bool
    {
        return in_array($this->selectedDutyObjectType, ['server', 'application'], true);
    }

    public function getShouldShowApplicationFieldProperty(): bool
    {
        return $this->selectedDutyObjectType === 'application';
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

        $existingCount = $this->report->photos()->count();

        foreach ($this->newPhotos as $photo) {
            if (($existingCount + count($this->photos)) >= 5) {
                $this->addError('photos', 'Total foto maksimal 5.');
                break;
            }

            $this->photos[] = $photo;
        }

        if ($this->application_id && ! $this->server_id) {
            $this->addError('server_id', 'Pilih server terlebih dahulu sebelum memilih aplikasi.');
            return;
        }

        if ($this->application_id && $this->server_id) {
            $applicationBelongsToServer = Application::query()
                ->where('id', $this->application_id)
                ->where('server_id', $this->server_id)
                ->exists();

            if (! $applicationBelongsToServer) {
                $this->addError('application_id', 'Aplikasi tidak sesuai dengan server yang dipilih.');
                return;
            }
        }

        $this->newPhotos = [];
        $this->photoInputKey++;
    }

    public function removeNewPhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function removeExistingPhoto($photoId)
    {
        $user = auth()->user();

        if (! $user->employee_id || $this->report->employee_id !== $user->employee_id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus foto laporan ini.');
        }

        $photo = DailyReportPhoto::where('daily_report_id', $this->report->id)
            ->where('id', $photoId)
            ->firstOrFail();

        $oldValues = $photo->toArray();

        ActivityLogger::log(
            module: 'daily_report_photo',
            action: 'delete',
            description: 'Menghapus foto laporan kerja harian',
            subject: $photo,
            oldValues: $oldValues
        );

        if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        $this->report->refresh();
        $this->report->load('photos');

        session()->flash('success', 'Foto laporan berhasil dihapus.');
    }

    public function update()
    {
        $currentPhotoCount = $this->report->photos()->count();

        $this->validate([
            'report_date' => ['required', 'date'],
            'duty_id' => ['required', 'exists:duties,id'],
            'server_id' => [
                $this->shouldShowServerField ? 'required' : 'nullable',
                'exists:servers,id',
            ],
            'application_id' => [
                $this->shouldShowApplicationField ? 'required' : 'nullable',
                'exists:applications,id',
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string'],

            'photos' => ['nullable', 'array', 'max:' . max(0, 5 - $currentPhotoCount)],
            'photos.*' => ['image', 'max:5120'],
        ], [
            'report_date.required' => 'Tanggal laporan wajib diisi.',
            'title.required' => 'Judul kegiatan wajib diisi.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'photos.max' => 'Total foto maksimal 5.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 5 MB per file.',
            'server_id.required' => 'Server wajib dipilih untuk tupoksi dengan objek Server atau Aplikasi.',
            'application_id.required' => 'Aplikasi wajib dipilih untuk tupoksi dengan objek Aplikasi.',
            'duty_id.required' => 'Tupoksi wajib dipilih.',
            'duty_id.exists' => 'Tupoksi tidak valid.',
        ]);

        $employeeDutyIds = DB::table('employee_duty')
            ->where('employee_id', auth()->user()->employee_id)
            ->pluck('duty_id')
            ->toArray();

        $isCurrentReportDuty = (int) $this->duty_id === (int) $this->report->duty_id;

        if (! in_array((int) $this->duty_id, array_map('intval', $employeeDutyIds), true) && ! $isCurrentReportDuty) {
            $this->addError('duty_id', 'Tupoksi tidak tersedia untuk pegawai ini.');
            return;
        }

        $user = auth()->user();

        if (! $user->employee_id || $this->report->employee_id !== $user->employee_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah laporan ini.');
        }

        if (! $this->shouldShowServerField) {
            $this->server_id = null;
        }

        if (! $this->shouldShowApplicationField) {
            $this->application_id = null;
        }

        if ($this->application_id && $this->server_id) {
            $applicationBelongsToServer = Application::query()
                ->where('id', $this->application_id)
                ->where('server_id', $this->server_id)
                ->exists();

            if (! $applicationBelongsToServer) {
                $this->addError('application_id', 'Aplikasi tidak sesuai dengan server yang dipilih.');
                return;
            }
        }

        $oldValues = $this->report->fresh()->toArray();

        $oldValues['photo_count'] = $this->report->photos()->count();

        $oldValues['photo_paths'] = $this->report->photos()
            ->orderBy('sort_order')
            ->pluck('file_path')
            ->toArray();

        $this->report->update([
            'duty_id' => $this->duty_id ?: null,
            'server_id' => $this->shouldShowServerField ? ($this->server_id ?: null) : null,
            'application_id' => $this->shouldShowApplicationField ? ($this->application_id ?: null) : null,
            'report_date' => $this->report_date,
            'title' => $this->title,
            'description' => $this->description,
            'notes' => $this->notes,
        ]);

        $this->storeCompressedPhotos();

        $this->report->refresh();

        $newValues = $this->report->fresh()->toArray();

        $newValues['photo_count'] = $this->report->photos()->count();

        $newValues['photo_paths'] = $this->report->photos()
            ->orderBy('sort_order')
            ->pluck('file_path')
            ->toArray();

        ActivityLogger::log(
            module: 'daily_report',
            action: 'update',
            description: $this->report->is_delegated
                ? 'Mengubah laporan kerja harian delegasi'
                : 'Mengubah laporan kerja harian',
            subject: $this->report,
            oldValues: $oldValues,
            newValues: $newValues
        );

        session()->flash('success', 'Laporan berhasil diperbarui.');

        return redirect()->route('pegawai.reports.show', $this->report);
    }

    private function storeCompressedPhotos(): void
    {
        if (empty($this->photos)) {
            return;
        }

        $manager = new ImageManager(new Driver());

        $startSortOrder = $this->report->photos()->max('sort_order') ?? 0;

        foreach ($this->photos as $index => $photo) {
            $directory = 'daily-reports/' . now()->format('Y/m');

            $filename = 'report-' . $this->report->id . '-' . uniqid() . '.jpg';
            $path = $directory . '/' . $filename;

            $image = $manager->read($photo->getRealPath())
                ->scaleDown(width: 1280);

            $encodedImage = $image->toJpeg(75);

            Storage::disk('public')->put($path, (string) $encodedImage);

            DailyReportPhoto::create([
                'daily_report_id' => $this->report->id,
                'file_path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'compressed_size' => Storage::disk('public')->size($path),
                'mime_type' => 'image/jpeg',
                'sort_order' => $startSortOrder + $index + 1,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.reports.edit-daily-report')
            ->layout('layouts.app');
    }
}