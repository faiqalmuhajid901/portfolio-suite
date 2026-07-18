<?php

namespace App\Livewire\Messages;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function markRead(int $messageId): void
    {
        $message = ContactMessage::query()->findOrFail($messageId);
        $message->update([
            'status' => ContactMessage::STATUS_READ,
            'read_at' => $message->read_at ?? now(),
        ]);
    }

    public function archive(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->update([
            'status' => ContactMessage::STATUS_ARCHIVED,
            'read_at' => now(),
        ]);
    }

    public function delete(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->delete();
        session()->flash('success', 'Pesan berhasil dihapus.');
    }

    public function render(): View
    {
        $messages = ContactMessage::query()
            ->when($this->status !== 'all', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';
                $query->where(function (Builder $subQuery) use ($term): void {
                    $subQuery
                        ->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('company', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('message', 'like', $term);
                });
            })
            ->orderByRaw("case when status = 'new' then 0 when status = 'read' then 1 else 2 end")
            ->latest()
            ->paginate(12);

        $newCount = ContactMessage::query()->new()->count();

        return view('livewire.messages.index', compact('messages', 'newCount'))
            ->layout('layouts.dashboard')
            ->title('Contact Inbox');
    }
}
