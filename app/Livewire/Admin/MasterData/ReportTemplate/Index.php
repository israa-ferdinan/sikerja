<?php

namespace App\Livewire\Admin\MasterData\ReportTemplate;

use App\Models\JobDuty;
use App\Models\ReportTemplate;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $reportTemplateId;
    public $unit_id;
    public $job_duty_id;
    public $title;
    public $category;
    public $description_template;
    public $result_template;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    protected function rules()
    {
        return [
            'unit_id' => 'nullable|exists:units,id',
            'job_duty_id' => 'nullable|exists:duties,id',
            'title' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'description_template' => 'required|string',
            'result_template' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $template = ReportTemplate::findOrFail($id);

        $this->reportTemplateId = $template->id;
        $this->unit_id = $template->unit_id;
        $this->job_duty_id = $template->job_duty_id;
        $this->title = $template->title;
        $this->category = $template->category;
        $this->description_template = $template->description_template;
        $this->result_template = $template->result_template;
        $this->is_active = (bool) $template->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ReportTemplate::updateOrCreate(
            ['id' => $this->reportTemplateId],
            [
                'unit_id' => $this->unit_id ?: null,
                'job_duty_id' => $this->job_duty_id ?: null,
                'title' => $this->title,
                'category' => $this->category,
                'description_template' => $this->description_template,
                'result_template' => $this->result_template,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash(
            'success',
            $this->isEdit ? 'Template laporan berhasil diperbarui.' : 'Template laporan berhasil ditambahkan.'
        );

        $this->closeModal();
    }

    public function delete($id)
    {
        ReportTemplate::findOrFail($id)->delete();

        session()->flash('success', 'Template laporan berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'reportTemplateId',
            'unit_id',
            'job_duty_id',
            'title',
            'category',
            'description_template',
            'result_template',
            'isEdit',
        ]);

        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $reportTemplates = ReportTemplate::query()
            ->with(['unit', 'jobDuty'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhere('description_template', 'like', '%' . $this->search . '%')
                        ->orWhere('result_template', 'like', '%' . $this->search . '%')
                        ->orWhereHas('unit', function ($unitQuery) {
                            $unitQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('jobDuty', function ($jobDutyQuery) {
                            $jobDutyQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $units = Unit::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $jobDuties = JobDuty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.master-data.report-template.index', [
            'reportTemplates' => $reportTemplates,
            'units' => $units,
            'jobDuties' => $jobDuties,
        ])->layout('layouts.app');
    }
}