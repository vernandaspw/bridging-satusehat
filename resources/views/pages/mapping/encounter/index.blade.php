@extends('layouts.app')

@section('title', 'Dokter')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

<!-- <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}"> -->
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Mapping Encounter</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dokter.index') }}">Master Data</a></div>
                <div class="breadcrumb-item">Practicioner</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('mapping.encounter.create')}}" class="btn btn-primary rounded-0">New Mapping</a>
                </div>
                <div class="card-body">
                    <table class="table-striped table" id="table-1">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Practitioner</th>
                                <th scope="col">Nama Practitioner</th>
                                <th scope="col">IHS Number</th>
                                {{-- <th scope="col">Organization ID</th> --}}
                                <th scope="col">Organization Name</th>
                                <th scope="col">Location Name</th>
                                <th scope="col">Encounter Type</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" width="5%">

                                </td>
                                {{-- <td>
                                    <div class="badge badge-success"> </div>
                                </td> --}}
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td width="15%">
                                    <a href="" class="btn btn-warning"><i class="far fa-edit"></i></a>
                                    <a href="" class="btn btn-info"><i class="fas fa-info-circle"></i></a>
                                </td>
                            </tr>
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

<!-- Page Specific JS File -->
<script src="{{ asset('js/page/modules-datatables.js') }}"></script>

<!-- Page Specific JS File -->
@endpush