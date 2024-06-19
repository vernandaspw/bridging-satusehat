<div>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Encounter Bundle - Ranap</h1>
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
                                <div class="col-md-9 filter-buttons">
                                    <div class="form-group d-flex align-items-end">
                                        <button type="button" wire:click='tanggal()' class="btn btn-success mr-2"
                                            style="margin-top: 30px;">Filter</button>
                                        <button wire:click="kirimPerTanggal" type="button" class="btn btn-primary mr-2"
                                            style="margin-top: 30px;">Send Data Base A Date (Prod)</button>
                                        <button wire:click="kirimPerTanggal2" type="button"
                                            class="btn btn-primary mr-2" style="margin-top: 30px;">Send Data Base A Date
                                            (Sim)</button>
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
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">EncounterID</th>
                                        @if (!env('IS_PROD'))
                                            <th scope="col">EncounterIDsanbox</th>
                                        @endif
                                        <th scope="col">status</th>
                                        {{-- <th>Oleh</th> --}}
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($registrations))
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
                                                @elseif ($item['status_rawat'] == 'RAWAT DARURAT')
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
                                            <td>
                                                @if($item['ParamedicIHS']!='')
                                                <div class="badge badge-success">{{ $item['kode_dokter'] }} </div>
                                                @else
                                                <div class="badge badge-danger">{{ $item['kode_dokter'] }} </div>
                                                @endif
                                            </td>
                                            <td>
                                            @if($item['location_id']!='')
                                                <div class="badge badge-success">{{ $item['ServiceUnitID'].'.  '.$item['location_name'] }} </div>
                                                @else
                                                <div class="badge badge-danger">{{ $item['ServiceUnitID'] }} </div>
                                                @endif
                                           </td>
                                            <td>{{ $item['ss_encounter_id'] }}</td>
                                            @if (!env('IS_PROD'))
                                            <td>{{ $item['ss_encounter_id_sanbox'] }}</td>
                                            @endif
                                            <td>{{ $item['log'] ? $item['log']['status'] : '-' }}</td>
                                            {{-- <td>{{ $item['log'] ? $item['log']['user']['name'] : '-' }}</td> --}}
                                            <td width="15%">
                                                {{-- <a href="http://" class="btn btn-info"><i class="fas fa-info-circle"></i></a> --}}
                                                <a href="javascript:void()"
                                                    wire:click="kirim('{{ $item['no_registrasi'] }}')"
                                                    class="btn btn-primary">Kirim</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if($registrations)

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
                            
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
