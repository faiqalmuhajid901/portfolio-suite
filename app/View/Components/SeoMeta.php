<?php

namespace App\View\Components;

use App\Support\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoMeta extends Component
{
    public function render(): View
    {
        return view(
            'components.seo-meta',
            [
                'seo' => app(
                    SeoManager::class
                )->data(),
            ]
        );
    }
}
