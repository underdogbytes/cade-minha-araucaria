<?php

namespace App\View\Components\Profile;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Feed extends Component
{
    /**
     * Create a new component instance.
     */
    public $observations;
    public function __construct($observations)
    {
        $this->observations = $observations;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile.feed');
    }
}
