@extends('layouts.app')

@section('title', $title)

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
                <div class="breadcrumb-item active"><a href="{{ route('location.index') }}">Master Data</a></div>
                <div class="breadcrumb-item">Location</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('location.create')}}" class="btn btn-primary rounded-0">New Location</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table-striped table" id="table-1">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        No
                                    </th>
                                    <th>Location ID</th>
                                    <th>identifier_value</th>
                                    <th>ServiceUnitID</th>
                                    <th>RoomID</th>
                                    <th>RoomCode</th>
                                    <th>Name</th>
                                    <th>Organization ID</th>
                                    <th>Description</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locations as $location)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $location->location_id}}</td>
                                    <td>{{ $location->identifier_value}}</td>
                                    <td>{{ $location->ServiceUnitID}}</td>
                                    <td>{{ $location->RoomID}}</td>
                                    <td>{{ $location->RoomCode}}</td>
                                    <td>{{ $location->name}}</td>
                                    <td>
                                        <a href="{{ route('organization.show', $location->organization_id)}}">{{ $location->organization_id}} </a>
                                    </td>
                                    <td>{{$location->description}}</td>
                                    <td>{{$location->updated_at}}</td>
                                    <td width="15%">
                                        <a href="{{ route('location.edit', $location->location_id) }}" class="btn btn-warning"><i class="far fa-edit"></i></a>
                                        <a href="{{ route('location.show', $location->location_id) }}" class="btn btn-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                              </svg></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
