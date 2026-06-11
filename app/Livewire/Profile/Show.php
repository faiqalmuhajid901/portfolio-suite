<?php

namespace App\Livewire\Profile;

use App\Models\Career;
use App\Models\Skill;
use Livewire\Component;

class Show extends Component
{
    public function render()
    {
        return view('livewire.profile.show', [
            'skills' => Skill::latest()->get(),
            'careers' => Career::latest()->get(),
        ])->layout('layouts.dashboard');
    }
}