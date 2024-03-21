<?php

namespace App\Http\Livewire\Encounter\Bundle;

use Livewire\Component;

class EncounterBundleRanapPage extends Component
{
    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-ranap-page')->extends('layouts.app')->section('main');
    }
}
