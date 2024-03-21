<?php

namespace App\Http\Livewire\Encounter\Bundle;

use Livewire\Component;

class EncounterBundleIgdPage extends Component
{
    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-igd-page')->extends('layouts.app')->section('main');
    }
}
