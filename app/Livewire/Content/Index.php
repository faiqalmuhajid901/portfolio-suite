<?php

namespace App\Livewire\Content;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function render(): View
    {
        return view('livewire.content.index')
            ->layout('layouts.dashboard')
            ->title('Professional Content');
    }
}
