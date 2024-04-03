@extends('layouts.docs')

@section('docs patient', $title)

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
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Documentation</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Instalasi</h4>
                        </div>
                        <div class="card-body">
                            <div id="accordion">
                                <!-- 2.1 -->
                                <ol>
                                    <li>
                                        Clone repo : https://github.com/vernandaspw/bridging-satusehat.git
                                    </li>
                                    <li>
                                        Jalankan : cd bridging-satusehat
                                    </li>
                                    <li>
                                        Jalankan : composer install
                                    </li>
                                    <li>
                                        Jalankan : cp .env.example .env
                                    </li>
                                    <li>
                                        Jalankan : php artisan key:generate command
                                    </li>
                                    <li>
                                        Sesuaikan configurasi pada .env
                                    </li>
                                    <li>
                                        Jalankan php artisan migrate --seed
                                    </li>
                                    <li>
                                        Jalankan php artisan serv
                                    </li>
                                    <li>
                                        Menyiapkan dan menyesuaikan api service dari simrs/rme/ll
                                    </li>
                                    <li>
                                        Sesuaikan response API SERVICE dari simrs anda dengan response yg tersedia
                                    </li>
                                </ol>


                                {{-- <div class="accordion">
                                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-1" aria-expanded="true">
                                    <h4>Perkenalan</h4>
                                </div>
                                <div class="accordion-body collapse" id="panel-body-1" data-parent="#accordion">
                                    awdaw
                                </div>
                            </div> --}}

                            </div>
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
@endpush
