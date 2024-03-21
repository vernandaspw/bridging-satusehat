<?php

namespace App\Http\Livewire\Encounter\Bundle;

use Livewire\Component;

class EncounterBundlePage extends Component
{
    public function render()
    {
        return view('livewire.encounter.bundle.encounter-bundle-page')->extends('layouts.app')->section('main');
    }
}
