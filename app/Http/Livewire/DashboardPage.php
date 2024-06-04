<?php

namespace App\Http\Livewire;

use App\Services\RS\RegistrationService;
use Livewire\Component;

class DashboardPage extends Component
{
    public $qyear;
    public $qmonth;
    protected $queryString = [
        'qyear' => ['except' => ''],
        'qmonth' => ['except' => ''],
    ];
    public function render()
    {
        return view('livewire.dashboard-page')->extends('layouts.app')->section('main');
    }

    public function mount()
    {
        if ($this->qyear) {
            $this->year = $this->qyear;
        } else {
            $this->year = date('Y');
        }
        if ($this->qmonth) {
            $this->month = $this->qmonth;
        } else {
            $this->month = date('m');
        }
        $this->pilihBulan = $this->qmonth;
        $this->pilihTahun = $this->qyear;
        // dd($this->pilihBulan);
        $this->fetch();

        $this->select_bulans = [];
        for ($i = 1; $i <= 12; $i++) {
            $this->select_bulans[] = $i;
        }

        $this->select_tahuns = [];
        $current_year = date("Y");
        for ($i = 2023; $i <= $current_year; $i++) {
            $this->select_tahuns[] = $i;
        }
    }

    public $select_bulans = [], $select_tahuns = [];

    public $pilihBulan, $pilihTahun;

    public $year, $month;

    public $rajals = [];

    public function fetch()
    {
        set_time_limit(9999);
        try {
            // dd($this->year, $this->month);
            $this->rajals = RegistrationService::getCount($this->year, $this->month);

        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }

    public function filter()
    {
        if (!$this->pilihBulan || !$this->pilihTahun) {
            return $this->emit('error', 'pilih bulan & tahun');
        }
        $this->rajals = null;
        $this->qmonth = $this->pilihBulan;
        $this->qyear = $this->pilihTahun;
        $this->month = $this->pilihBulan;
        $this->year = $this->pilihTahun;

        redirect('dashboard?qyear=' . $this->qyear . '&qmonth=' . $this->qmonth);
    }
}
