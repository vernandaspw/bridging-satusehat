<div>

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Encounter Bundle - Rawat Jalan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('pendaftaran.index') }}">Kunjungan</a></div>
                    <div class="breadcrumb-item">Pendaftaran</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card mb-2">
                    <div class="card-body">
                        <form id="filterForm" action="" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal</label>
                                        <input type="date" class="form-control" id="tanggal" wire:model="tanggal"
                                            value="{{ request('tanggal') }}">
                                    </div>
                                </div>
                                <div class="col-md-3 filter-buttons">
                                    <div class="form-group d-flex align-items-end">
                                        <button type="button" wire:click='tanggal()' class="btn btn-success mr-2"
                                            style="margin-top: 30px;">Filter</button>
                                        <button wire:click="syncPerTanggal" type="button" class="btn btn-primary mr-2"
                                            style="margin-top: 30px;">Kirim data yg discharge Per Tanggal</button>
                                    </div>
                                </div>
                            </div>
                            <div class="">Data Belum Terkirim {{ $registrations['not_encounter'] }}</div>
                            <div class="">Encounter Terkirim {{ $registrations['encounter'] }}</div>
                        </form>
                    </div>
                </div>
                <center>
                    <div class="text-center my-2" wire:loading>
                        <div class="spinner-border text-primary" role="status">
                        </div>
                        <div class="">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </center>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table-sm table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Jenis</th>
                                        <th scope="col">No Reg</th>
                                        <th scope="col">No MR</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">NIK</th>
                                        <th scope="col">Rekanan</th>
                                        <th scope="col">Kode dokter</th>
                                        <th scope="col">ServiceUnitID</th>
                                        <th scope="col">RoomCode</th>
                                        <th scope="col">RoomName</th>
                                        <th scope="col">EncounterID</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registrations['items'] as $item)
                                        <tr>
                                            <td class="text-center" width="5%">
                                                {{ $loop->iteration + $registrations['from'] - 1 }}
                                            </td>
                                            <td>
                                                @if ($item['status_rawat'] == 'RAWAT JALAN')
                                                    <div class="badge badge-success">RJ</div>
                                                @elseif ($item['status_rawat'] == 'RAWAT INAP')
                                                    <div class="badge badge-success">RI</div>
                                                @elseif ($item['status_rawat'] == 'ER')
                                                    <div class="badge badge-success">ER</div>
                                                @else
                                                    <div class="badge badge-info">Lainnya</div>
                                                @endif
                                            </td>
                                            <td>{{ $item['no_registrasi'] }}</td>
                                            <td>{{ $item['no_mr'] }}</td>
                                            <td>{{ $item['nama_pasien'] }}</td>
                                            <td>{{ $item['nik'] }}</td>

                                            <td>{{ $item['nama_rekanan'] }}</td>
                                            <td>{{ $item['kode_dokter'] }}</td>
                                            <td>{{ $item['ServiceUnitID'] }}</td>
                                            <td>{{ $item['RoomCode'] }}</td>
                                            <td>{{ $item['RoomName'] }}</td>
                                            <td>{{ $item['ss_encounter_id'] }}</td>
                                            <td width="15%">
                                                {{-- <a href="http://" class="btn btn-info"><i class="fas fa-info-circle"></i></a> --}}
                                                <a href="javascript:void()" wire:click="kirim('{{ $item['no_registrasi'] }}')"
                                                    class="btn btn-primary">Kirim</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center align-content-center">
                                <div class="">Halaman saat ini {{ $registrations['current_page'] }} | Total data
                                    {{ $registrations['total'] }}</div>
                                <div class="d-flex">
                                    <a wire:click="page(1)" class="btn btn-primary m-1">first page</a>
                                    @if ($registrations['prev_page_url'])
                                        <a wire:click="page({{ explode('=', parse_url($registrations['prev_page_url'])['query'])[1] }})"
                                            class="btn btn-primary m-1">prev page</a>
                                    @endif
                                    @foreach ($registrations['links'] as $link)
                                        @if (is_array($link))
                                            @foreach ($link as $pageNumber => $url)
                                                <a wire:click="page({{ $pageNumber }})"
                                                    class="btn
                                                    @if ($registrations['current_page'] == $pageNumber) btn-primary
                                                    @else
                                                    btn-secondary @endif
                                                    m-1">{{ $pageNumber }}</a>
                                            @endforeach
                                        @else
                                            <a class="btn btn-text m-1">...</a>
                                        @endif
                                    @endforeach
                                    @if ($registrations['next_page_url'])
                                        <a wire:click="page({{ explode('=', parse_url($registrations['next_page_url'])['query'])[1] }})"
                                            class="btn btn-primary m-1">next page</a>
                                    @endif
                                    <a wire:click="page({{ $registrations['last_page'] }})"
                                        class="btn btn-primary m-1">last
                                        page</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>



@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}"> -->

    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('library/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>



    <script src="{{ asset('library/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>

    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
    <script>
        function resetForm() {
            document.getElementById("filterForm").value = "";
            alert('Filter telah direset!');
            window.location.href = "{{ route('pendaftaran.index') }}";
        }
    </script>
@endpush
