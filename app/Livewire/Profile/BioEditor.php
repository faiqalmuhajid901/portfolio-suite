<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BioEditor extends Component
{
    public string $bio = '';

    public function mount(): void
    {
        $this->bio = Auth::user()?->profile?->bio
            ?? 'Tuliskan ringkasan profil profesional Anda.';
    }

    public function save(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->validate([
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $profile = $user->profile;

        if (! $profile) {
            $profile = $user->profile()->create([
                'name' => $user->name,
                'role' => 'Web Developer',
                'is_public' => true,
            ]);
        }

        /*
         * Hanya memperbarui bio.
         * Jangan menimpa name atau role.
         */
        $profile->update([
            'bio' => trim($this->bio),
        ]);

        session()->flash(
            'bio_success',
            'Bio berhasil diperbarui.'
        );
    }

    public function render()
    {
        return view('livewire.profile.bio-editor');
    }
}
