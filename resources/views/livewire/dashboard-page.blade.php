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

                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('myChart');
        const chartDatas = @json($rajals);

        const labels = chartDatas.map(e => e.tanggal);
        const countIHS = chartDatas.map(entry => entry.countIHS);
        const countNoIHS = chartDatas.map(entry => entry.countNoIHS);
        const countTotal = chartDatas.map(entry => entry.countTotal);

        new Chart(ctx, {
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
    </script>

</div>
