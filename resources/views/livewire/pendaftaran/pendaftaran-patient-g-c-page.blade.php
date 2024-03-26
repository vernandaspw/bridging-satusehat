<div>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Master Patient</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('patient.index') }}">Master Data</a></div>
                    <div class="breadcrumb-item">Patient</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <form id="filterForm" action="" method="get">
                            <div class="row">
                                <div class="col-sm-12 col-md-2">
                                    <div class="form-group">
                                        <label for="MedicalNo">No MR</label>
                                        <input type="text" class="form-control" id="MedicalNo" name="MedicalNo"
                                            value="{{ request('MedicalNo') }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="nik">NIK</label>
                                        <input type="text" class="form-control" id="nik" name="nik"
                                            value="{{ request('nik') }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="bpjs">BPJS</label>
                                        <input type="text" class="form-control" id="bpjs" name="no_bpjs"
                                            value="{{ request('no_bpjs') }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    <div class="form-group">
                                        <label for="nama">Name</label>
                                        <input type="text" class="form-control" id="nama" name="nama"
                                            value="{{ request('nama') }}">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-2 filter-buttons">
                                    <div class="form-group d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary mr-2"
                                            style="margin-top: 30px;">Filter</button>
                                        <button type="button" class="btn btn-danger" style="margin-top: 30px;"
                                            onclick="resetForm()"> Reset</button>
                                    </div>
                                </div>
                            </div>
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
                    <div class="card-header">
                        <a href="" class="btn btn-info rounded-0"><svg xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9" />
                                <path fill-rule="evenodd"
                                    d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                            </svg> Sync data</a>
                        <div class="mr-1"></div>
                        <a href="" class="btn btn-primary rounded-0">Kirim Pasien Baru (IHS Tidak ditemukan)</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table" >
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No MR</th>
                                        <th>Name</th>
                                        <th>BPJS</th>
                                        <th>NIK</th>
                                        <th>Gender</th>
                                        <th>tgl lahir</th>
                                        <th>Pernyataan GC SS</th>
                                        <th>IHS</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['items'] as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="badge badge-success">{{ $item['MedicalNo'] }}</div>
                                            </td>
                                            <td width="30%">{{ $item['nama_pasien'] }}</td>
                                            <td>{{ $item['no_bpjs'] }}</td>
                                            <td>{{ $item['nik'] }}</td>
                                            <td>
                                                @if ($item['jenis_kelamin'] == 'L')
                                                    <div>L</div>
                                                @else
                                                    <div>P</div>
                                                @endif
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($item['tanggal_lahir'])) }}</td>

                                            <td>
                                                @if (!empty($item['isGCSatusehat']))
                                                    {{ $item['isGCSatusehat'] == 1 ? 'telah setuju' : 'tidak' }}
                                                @endif
                                            </td>
                                            <td>{{ $item['ihs'] }}</td>
                                            <td width="15%">
                                                {{-- <a href="{{ route('patient.show', $item['MedicalNo'])}}" class="btn btn-info"><i class="fas fa-info-circle"></i></a> --}}
                                                @if ($item['nik'])
                                                    <a href="" class="btn btn-primary">setujui general concent &
                                                        get IHS</a>
                                                    {{-- @if (!empty($item['isGCSatusehat']) && $item['isGCSatusehat'] == 1)
                                                        <a href=""
                                                            class="btn btn-primary">buat IHS</a>
                                                    @endif --}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center align-content-center">
                                <div class="">Halaman saat ini {{ $data['current_page'] }} | Total data
                                    {{ $data['total'] }}</div>
                                <div class="d-flex">
                                    <a wire:click="page(1)" class="btn btn-primary m-1">first page</a>
                                    @if ($data['prev_page_url'])
                                        <a wire:click="page({{ explode('=', parse_url($data['prev_page_url'])['query'])[1] }})"
                                            class="btn btn-primary m-1">prev page</a>
                                    @endif
                                    @foreach ($data['links'] as $link)
                                        @if (is_array($link))
                                            @foreach ($link as $pageNumber => $url)
                                                <a wire:click="page({{ $pageNumber }})"
                                                    class="btn
                                                    @if ($data['current_page'] == $pageNumber) btn-primary
                                                    @else
                                                    btn-secondary @endif
                                                    m-1">{{ $pageNumber }}</a>
                                            @endforeach
                                        @else
                                            <a class="btn btn-text m-1">...</a>
                                        @endif
                                    @endforeach
                                    @if ($data['next_page_url'])
                                        <a wire:click="page({{ explode('=', parse_url($data['next_page_url'])['query'])[1] }})"
                                            class="btn btn-primary m-1">next page</a>
                                    @endif
                                    <a wire:click="page({{ $data['last_page'] }})"
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
    <link rel="stylesheet" href="{{ asset('library/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('library/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('library/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/modules-datatables.js') }}"></script>


    <script>
        function resetForm() {
            document.getElementById("filterForm").value = "";
            alert('Filter telah direset!');
            window.location.href = "{{ route('patient.index') }}";
        }
    </script>
@endpush
