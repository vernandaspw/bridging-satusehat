<div>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('pendaftaran.index') }}">Kunjungan</a></div>
                    <div class="breadcrumb-item">Pendaftaran</div>
                </div>
            </div>
            <div class="section-body">
                <div class="card">
                    <div class="card-header bg-primary d-flex justify-content-between">
                        <div class="">
                            <b>RAJAL</b>
                        </div>
                        <div class="d-flex">
                            <form wire:submit.prevent='filter'>
                                <select wire:model.defer='pilihBulan' id="">
                                    <option value="">Pilih bulan</option>
                                    @foreach ($select_bulans as $select_bulan)
                                        <option value="{{ $select_bulan }}">{{ $select_bulan }}</option>
                                    @endforeach
                                </select>
                                <select wire:model.defer="pilihTahun" id="">
                                    <option value="">Pilih tahun</option>
                                    @foreach ($select_tahuns as $select_tahun)
                                        <option value="{{ $select_tahun }}">{{ $select_tahun }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn bg-white ml-2">Filter</button>
                                <div class="text-danger" wire:loading>Loading..</div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($rajals != null)
                            <div>
                                <canvas id="myChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script src="{{ asset('lib/sweatalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        Livewire.on('success', data => {
            console.log(data);
            Swal.fire({
                position: 'center',
                title: 'berhasil!',
                text: data,
                icon: 'success',
                confirmButtonText: 'oke'
                // showConfirmButton: false
                // , timer: 1500
            })
        })
        Livewire.on('error', data => {
            console.log(data);
            Swal.fire({
                position: 'center',
                title: 'gagal!',
                text: data,
                icon: 'error',
                confirmButtonText: 'oke'
                // showConfirmButton: false
                // , timer: 1500
            })
        })
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @if ($rajals != null)
        <script>
            document.addEventListener('livewire:load', function() {
                let chart;
                const ctx = document.getElementById('myChart');

                renderChart(@json($rajals));

                function renderChart(chartDatas) {
                    const labels = chartDatas.map(e => e.tanggal);
                    const countIHS = chartDatas.map(entry => entry.countIHS);
                    const countNoIHS = chartDatas.map(entry => entry.countNoIHS);
                    const countTotal = chartDatas.map(entry => entry.countTotal);

                    chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                type: 'bar',
                                label: 'Terkirim',
                                data: countIHS,
                                borderColor: 'rgb(0, 255, 115)',
                                backgroundColor: 'rgba(0, 255, 102)'
                            }, {
                                type: 'bar',
                                label: 'belum terkirim',
                                data: countNoIHS,
                                borderColor: 'rgb(255, 13, 0)',
                                backgroundColor: 'rgba(255, 13, 0)'
                            }, {
                                type: 'line',
                                label: 'Total',
                                data: countTotal,
                                fill: false,
                                borderColor: 'rgb(0, 110, 255)',
                                backgroundColor: 'rgb(0, 110, 255)'
                            }]
                        },
                    });

                }

            });
        </script>
    @endif
</div>
