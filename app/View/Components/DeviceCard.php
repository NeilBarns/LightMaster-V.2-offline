<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DeviceCard extends Component
{
    public $device;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($device)
    {
        $this->device = $device;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.device-card');
    }
}
