<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{   
    // colores
    public 
            $primaryColor ="rgb(71 85 105 / var(--tw-bg-opacity, 1))",
            $secondaryColor ="#3b82f6",
            $white= '#ffffff',
            $black= '#000000'
            ;


    public function render(): View
    {
        return view('layouts.app');
    }
}
