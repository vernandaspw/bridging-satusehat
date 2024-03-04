@extends('layouts.app')

@section('title', 'Pendaftaran')

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

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Pendaftaran</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Kunjungan</a></div>
                <div class="breadcrumb-item">Pendaftaran</div>
            </div>
        </div>

        <div class="section-body">
            <form id="filterForm" action="" action="" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kode_dokter">Pilih Dokter</label>
                            <select class="form-control select2" id="kode_dokter" name="kode_dokter">
                                <option value="" selected disabled>-- silahkan pillih --</option>
                                @foreach ($dokters as $dokter)
                                <option value="{{ $dokter['kode_dokter'] }}">{{ $dokter['nama_dokter'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal">Tanggal <small>(kosongkan jika tanggal saat ini)</small></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status_rawat">Status Rawat </label>
                            <select class="form-control selectric" id="status_rawat" name="status_rawat">
                                <option value="" selected disabled>-- silahkan pillih --</option>
                                <option value="RAWAT JALAN">Rawat Jalan</option>
                                <option value="RAWAT INAP">Rawat Inap</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 filter-buttons">
                        <div class="form-group d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2" style="margin-top: 30px;"><i class="fas fa-filter"></i> Filter</button>
                            <button type="button" class="btn btn-danger" style="margin-top: 30px;" onclick="resetForm()"><i class="fas fa-sync"></i> Reset</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header">
                    <h4>Pendaftaran</h4>
                </div>
                <div class="card-body">
                    <table class="table-striped table" id="table-1">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">No Reg</th>
                                <th scope="col">No MR</th>
                                <th scope="col">Nama Pasien</th>
                                <th scope="col">NIK</th>
                                <th scope="col">Jenis Pasien</th>
                                <th scope="col">Rekanan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td class="text-center" width="5%">
                                    {{ $loop->iteration }}
                                </td>
                                <td>{{ $item['no_registrasi'] }}</td>
                                <td>{{ $item['no_mr'] }}</td>
                                <td>{{ $item['nama_pasien'] }}</td>
                                <td>{{ $item['nik'] }}</td>
                                <td>
                                    @if ($item['status_rawat'] == 'RAWAT JALAN')
                                    <div class="badge badge-success">RJ</div>
                                    @else
                                    <div class="badge badge-info">RI</div>
                                    @endif
                                </td>
                                <td>{{ $item['nama_rekanan'] }}</td>
                                <td width="10%">
                                    <a href="http://" class="btn btn-warning"><i class="far fa-eye"></i></a>
                                    <a href="http://" class="btn btn-primary">sync</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

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
@endpush