<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Feed extends Component
{
    public $observations; 

    public function __construct($observations)
    {
        $this->observations = $observations;
    }

    public function render(): View
    {
        return view('components.feed');
    }
}