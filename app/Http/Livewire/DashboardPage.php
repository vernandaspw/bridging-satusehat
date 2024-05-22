<?php

namespace App\Http\Livewire;

use App\Services\RS\RegistrationService;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render()
    {
        return view('livewire.dashboard-page')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        $this->year = date('Y');
        $this->month = date('m');

        $this->fetch();
    }
    public $year, $month;

    public $rajals = [];

    public function fetch()
    {
        try {
            $this->rajals = RegistrationService::getCount($this->year, $this->month);

        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }
}
