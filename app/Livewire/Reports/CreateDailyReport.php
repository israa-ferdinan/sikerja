<?php

namespace App\Livewire\Reports;

use App\Models\Application;
use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\Duty;
use App\Models\Server;
use App\Models\ReportTemplate;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

//use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use Livewire\Component;
use Livewire\WithFileUploads;

class CreateDailyReport extends Component
{
    use WithFileUploads;
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

    public $template_id;
    public $templates = [];

    public function mount()
    {
        $this->report_date = now()->format('Y-m-d');

        $this->duties = Duty::orderBy('name')->get();
        $this->servers = Server::orderBy('name')->get();

        $this->applications = collect();

        $this->templates = ReportTemplate::query()
        ->where('is_active', 1)
        ->orderBy('title')
        ->get();

        $this->applications = collect();
    }

    public function updatedServerId()
    {
        $this->application_id = null;

        if ($this->server_id) {
            $this->applications = Application::where('server_id', $this->server_id)
                ->orderBy('name')
                ->get();
        } else {
            $this->applications = collect();
        }
    }

    public function save()
    {
        $this->validate([
            'report_date' => ['required', 'date'],
            'duty_id' => ['nullable', 'exists:duties,id'],
            'server_id' => ['nullable', 'exists:servers,id'],
            'application_id' => ['nullable', 'exists:applications,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string'],

            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:5120'],
        ], [
            'report_date.required' => 'Tanggal laporan wajib diisi.',
            'report_date.date' => 'Format tanggal laporan tidak valid.',
            'duty_id.exists' => 'Tupoksi tidak valid.',
            'server_id.exists' => 'Server tidak valid.',
            'application_id.exists' => 'Aplikasi tidak valid.',
            'title.required' => 'Judul kegiatan wajib diisi.',
            'title.max' => 'Judul kegiatan maksimal 255 karakter.',
            'description.required' => 'Deskripsi kegiatan wajib diisi.',
            'photos.max' => 'Maksimal upload 5 foto.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 5 MB per file.',
        ]);

        $user = Auth::user();

        $employeeId = null;
        $unitId = null;

        if ($user->employee) {
            $employeeId = $user->employee->id;
            $unitId = $user->employee->unit_id ?? null;
        }

       $report = DailyReport::create([
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'unit_id' => $unitId,
            'duty_id' => $this->duty_id ?: null,
            'server_id' => $this->server_id ?: null,
            'application_id' => $this->application_id ?: null,
            'report_date' => $this->report_date,
            'title' => $this->title,
            'description' => $this->description,
            'notes' => $this->notes,
            'status' => 'submitted',
        ]);

        $this->storeCompressedPhotos($report);

        session()->flash('success', 'Laporan kerja harian berhasil disimpan.');

        return redirect()->route('pegawai.reports.create');
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

    public function updatedNewPhotos()
    {
        foreach ($this->newPhotos as $photo) {
            if (count($this->photos) >= 5) {
                break;
            }

            $this->photos[] = $photo;
        }

        $this->newPhotos = [];
        $this->photoInputKey++;
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);

        $this->photos = array_values($this->photos);
    }

    public function updatedTemplateId($value): void
    {
        if (empty($value)) {
            return;
        }

        $template = ReportTemplate::find($value);

        if (!$template) {
            return;
        }

        $replacements = $this->getTemplateReplacements();

        if (!empty($template->title)) {
            $this->title = $this->replaceTemplatePlaceholders($template->title, $replacements);
        }

        if (!empty($template->description_template)) {
            $this->description = $this->replaceTemplatePlaceholders($template->description_template, $replacements);
        }

        if (!empty($template->result_template)) {
            $this->notes = $this->replaceTemplatePlaceholders($template->result_template, $replacements);
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
        $application = $this->application_id
            ? Application::find($this->application_id)
            : null;

        $server = $this->server_id
            ? Server::find($this->server_id)
            : null;

        $duty = $this->duty_id
            ? Duty::find($this->duty_id)
            : null;

        return [
            '{{application_name}}' => $application?->name ?? '-',
            '{{server_name}}' => $server?->name ?? '-',
            '{{duty_name}}' => $duty?->name ?? '-',
            '{{report_date}}' => $this->report_date
                ? Carbon::parse($this->report_date)->translatedFormat('d F Y')
                : '-',
        ];
    }

    public function cloneLastReport()
    {
        $lastReport = DailyReport::query()
            ->where('user_id', Auth::id())
            ->latest('report_date')
            ->latest('id')
            ->first();

        if (!$lastReport) {
            session()->flash('error', 'Belum ada laporan sebelumnya untuk diclone.');
            return;
        }

        $this->duty_id = $lastReport->duty_id;
        $this->server_id = $lastReport->server_id;

        $this->applications = $this->server_id
            ? Application::where('server_id', $this->server_id)->orderBy('name')->get()
            : collect();

        $this->application_id = $lastReport->application_id;
        $this->title = $lastReport->title;
        $this->description = $lastReport->description;
        $this->notes = $lastReport->notes;

        session()->flash('success', 'Laporan terakhir berhasil diclone. Silakan sesuaikan sebelum disimpan.');
    }

    public function render()
    {
        return view('livewire.reports.create-daily-report')
            ->layout('layouts.app');
    }
}