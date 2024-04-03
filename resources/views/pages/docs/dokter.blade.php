@extends('layouts.docs')

@section('docs patient', $title)

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

    <!-- <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}"> -->

    <style>
        pre {
            background-color: #f4f4f4;
            border-radius: 5px;

            padding: 0;
            /* menghilangkan padding */
        }
    </style>
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
                            <h4>{{ $title }}</h4>
                        </div>
                        <div class="card-body">
                            <div id="accordion">
                                <p>
                                    Sesuaikan response API SERVICE dari simrs anda dengan response yg tersedia
                                </p>
                                <div class="accordion">
                                    <div class="accordion-header" role="button" data-toggle="collapse"
                                        data-target="#panel-body-1" aria-expanded="true">
                                        <h4>GET : PASIEN - GET</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-1" data-parent="#accordion">

                                                    <!-- Kode API -->
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <b id="api-endpoint-1">API Endpoint : BRIDGING_SATUSEHAT_SERVICE_URL/pasien</b>
                                                            <pre style="bg-danger"><code class="language-php">
params :
- MedicalNo
- BpjsCardNo
- SSN
- PatienName
- take

Response :
{
    "status": true,
    "message": "success",
    "data": [
        {
            "id": "-",
            "ihs": null,
            "ihs_sanbox": null,
            "no_mr": "00-00000",
            "nama_pasien": "JhonDoe",
            "no_bpjs": null,
            "nik": "00000000000000",
            "no_hp": "-",
            "tanggal_lahir": "1900-09-12 00:00:00.000",
            "jenis_kelamin": "L"
        },
    ]
}
                                                    </code>
                                                </pre>


                                                        </div>
                                                    </div>



                                    </div>
                                </div>


                                <div class="accordion">
                                    <div class="accordion-header" role="button" data-toggle="collapse"
                                        data-target="#panel-body-2" aria-expanded="true">
                                        <h4>GET : PASIEN - GET BY NIK</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-2" data-parent="#accordion">

                                                    <!-- Kode API -->
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <b id="api-endpoint-1">API Endpoint : BRIDGING_SATUSEHAT_SERVICE_URL/pasien/detail/{nik}</b>
                                                            <b id="api-endpoint-1">Response</b>
                                                            <pre style="bg-danger"><code class="language-php">

Response :
{
    "status": true,
    "message": "success",
    "data": {
        "id": "-",
        "ihs": null,
        "ihs_sanbox": null,
        "no_mr": "00000000",
        "nama_pasien": "Jhon doe",
        "no_bpjs": null,
        "nik": "00000000000000000000",
        "no_hp": "-",
        "tanggal_lahir": "1900-09-12 00:00:00.000",
        "jenis_kelamin": "L"
    }
}
                                                    </code>
                                                </pre>


                                                        </div>
                                                    </div>



                                    </div>
                                </div>




                                <div class="accordion">
                                    <div class="accordion-header" role="button" data-toggle="collapse"
                                        data-target="#panel-body-3" aria-expanded="true">
                                        <h4>POST : PASIEN - UPDATE IHS BY NOMOR REKAM MEDIS</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-3" data-parent="#accordion">

                                                    <!-- Kode API -->
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <b id="api-endpoint-1">API Endpoint : BRIDGING_SATUSEHAT_SERVICE_URL/pasien/ihs/{norm}</b>

                                                            <pre style="bg-danger"><code class="language-php">
query : {
    isProd : true / false
}

Response :
{
    "status": true,
    "message": "success",
    "data": {
        "id": "-",
        "ihs": null,
        "ihs_sanbox": null,
        "no_mr": "00000000",
        "nama_pasien": "Jhon doe",
        "no_bpjs": null,
        "nik": "00000000000000000000",
        "no_hp": "-",
        "tanggal_lahir": "1900-09-12 00:00:00.000",
        "jenis_kelamin": "L"
    }
}
                                                    </code>
                                                </pre>


                                                        </div>
                                                    </div>



                                    </div>
                                </div>

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
