<?php

namespace App\Livewire\Reports;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\Duty;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $this->report = $report->load('photos');

        $this->report_date = $report->report_date?->format('Y-m-d');
        $this->duty_id = $report->duty_id;
        $this->server_id = $report->server_id;
        $this->application_id = $report->application_id;
        $this->title = $report->title;
        $this->description = $report->description;
        $this->notes = $report->notes;

        $this->duties = Duty::orderBy('name')->get();
        $this->servers = Server::orderBy('name')->get();

        $this->applications = $this->server_id
            ? Application::where('server_id', $this->server_id)->orderBy('name')->get()
            : collect();
    }

    public function updatedServerId()
    {
        $this->application_id = null;

        $this->applications = $this->server_id
            ? Application::where('server_id', $this->server_id)->orderBy('name')->get()
            : collect();
    }

    public function updatedNewPhotos()
    {
        $existingCount = $this->report->photos()->count();

        foreach ($this->newPhotos as $photo) {
            if (($existingCount + count($this->photos)) >= 5) {
                break;
            }

            $this->photos[] = $photo;
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
        $photo = DailyReportPhoto::where('daily_report_id', $this->report->id)
            ->where('id', $photoId)
            ->firstOrFail();

        if ($photo->file_path && Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        $this->report->refresh();
        $this->report->load('photos');
    }

    public function update()
    {
        $currentPhotoCount = $this->report->photos()->count();

        $this->validate([
            'report_date' => ['required', 'date'],
            'duty_id' => ['nullable', 'exists:duties,id'],
            'server_id' => ['nullable', 'exists:servers,id'],
            'application_id' => ['nullable', 'exists:applications,id'],
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
        ]);

        if ($this->report->user_id !== Auth::id()) {
            abort(403);
        }

        $this->report->update([
            'duty_id' => $this->duty_id ?: null,
            'server_id' => $this->server_id ?: null,
            'application_id' => $this->application_id ?: null,
            'report_date' => $this->report_date,
            'title' => $this->title,
            'description' => $this->description,
            'notes' => $this->notes,
        ]);

        $this->storeCompressedPhotos();

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