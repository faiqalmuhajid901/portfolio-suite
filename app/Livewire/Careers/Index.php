<?php

namespace App\Livewire\Careers;

use App\Models\Career;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public ?int $careerId = null;
    public string $title = '';
    public string $company = '';
    public string $employmentType = '';
    public string $startDate = '';
    public string $endDate = '';
    public bool $isCurrent = false;
    public string $location = '';
    public string $description = '';
    public string $achievementsInput = '';
    public string $technologiesInput = '';
    public bool $isPublic = true;
    public int $sortOrder = 0;

    public function edit(int $careerId): void
    {
        $career = $this->careerQuery()->findOrFail($careerId);

        $this->careerId = $career->id;
        $this->title = (string) $career->title;
        $this->company = (string) $career->company;
        $this->employmentType = (string) $career->employment_type;
        $this->startDate = $career->start_date?->format('Y-m-d') ?? '';
        $this->endDate = $career->end_date?->format('Y-m-d') ?? '';
        $this->isCurrent = (bool) $career->is_current;
        $this->location = (string) $career->location;
        $this->description = (string) $career->description;
        $this->achievementsInput = implode("\n", $career->achievements ?? []);
        $this->technologiesInput = implode(', ', $career->technologies ?? []);
        $this->isPublic = (bool) $career->is_public;
        $this->sortOrder = (int) $career->sort_order;
        $this->resetValidation();
    }

    public function save(): void
    {
        if ($this->isCurrent) {
            $this->endDate = '';
        }

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'company' => ['required', 'string', 'max:160'],
            'employmentType' => ['nullable', 'string', 'max:60'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'isCurrent' => ['boolean'],
            'location' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'achievementsInput' => ['nullable', 'string', 'max:5000'],
            'technologiesInput' => ['nullable', 'string', 'max:1000'],
            'isPublic' => ['boolean'],
            'sortOrder' => ['integer', 'min:0', 'max:999'],
        ]);

        $payload = [
            'user_id' => Auth::id(),
            'title' => trim($validated['title']),
            'company' => trim($validated['company']),
            'employment_type' => $this->nullableTrim($validated['employmentType'] ?? null),
            'period' => $this->makeLegacyPeriod(),
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'] ?: null,
            'is_current' => (bool) $validated['isCurrent'],
            'location' => $this->nullableTrim($validated['location'] ?? null),
            'description' => trim($validated['description']),
            'achievements' => $this->prepareLines($validated['achievementsInput'] ?? ''),
            'technologies' => $this->prepareTechnologies($validated['technologiesInput'] ?? ''),
            'is_public' => (bool) $validated['isPublic'],
            'sort_order' => (int) $validated['sortOrder'],
        ];

        if ($this->careerId) {
            $this->careerQuery()->findOrFail($this->careerId)->update($payload);
            $message = 'Pengalaman kerja berhasil diperbarui.';
        } else {
            Career::query()->create($payload);
            $message = 'Pengalaman kerja berhasil ditambahkan.';
        }

        session()->flash('success', $message);
        $this->resetForm();
    }

    public function delete(int $careerId): void
    {
        $this->careerQuery()->findOrFail($careerId)->delete();
        session()->flash('success', 'Pengalaman kerja berhasil dihapus.');

        if ($this->careerId === $careerId) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->isPublic = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        $careers = $this->careerQuery()
            ->orderBy('sort_order')
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        return view('livewire.careers.index', compact('careers'))
            ->layout('layouts.dashboard')
            ->title('Career Timeline');
    }

    private function careerQuery(): Builder
    {
        return Career::query()->where('user_id', Auth::id());
    }

    private function makeLegacyPeriod(): string
    {
        $start = Carbon::parse($this->startDate)->format('M Y');
        $end = $this->isCurrent || $this->endDate === ''
            ? 'Present'
            : Carbon::parse($this->endDate)->format('M Y');

        return $start.' — '.$end;
    }

    private function prepareLines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function prepareTechnologies(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
