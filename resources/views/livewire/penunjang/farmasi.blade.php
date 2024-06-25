@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

<!-- <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}"> -->

@endpush

<div>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Penunjang - Farmasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Penunjang</a></div>
                    <div class="breadcrumb-item">Farmasi</div>
                </div>
            </div>

            <div class="section-body">
               
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
                            <table class="table-striped table" id="table-1">
                                <thead>
                                    <tr>
                                        <th scope="col">ItemID</th>
                                        <th scope="col">ItemCode</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">KFA+</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($Farmasix))
                                    @foreach ($Farmasix['items'] as $item)
                                        <tr>
                                            <td>{{ $item['ItemID'] }}</td>
                                            <td>{{ $item['ItemCode'] }}</td>
                                            <td>{{ $item['ItemName1'] }}</td>
                                            <td>{{ $item['ItemID'] }}</td>
                                            <td width="15%">
                                                <a href="javascript:void()"
                                                    wire:click="kirim('{{ $item['ItemID'] }}')"
                                                    class="btn btn-primary">Hapus KFA</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@push('scripts')
<!-- JS Libraies -->
<script src="{{ asset('library/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('library/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('library/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="{{ asset('js/page/modules-datatables.js') }}"></script>



@endpush
