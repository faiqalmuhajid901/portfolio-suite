<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BioEditor extends Component
{
    public string $bio = '';

    public function mount(): void
    {
        $this->bio = Auth::user()?->profile?->bio ?? 'Strategic portfolio manager focused on clean design systems, measurable project outcomes, and high-performance digital interfaces.';
    }

    public function save(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'role' => 'Portfolio Manager',
                'bio' => $this->bio,
            ]
        );

        session()->flash('bio_success', 'Bio berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.profile.bio-editor');
    }
}