@extends('layouts.app')

@section('title', $title)

@push('style')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
     --}}
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('location.index') }}">Master Data</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('location.index') }}">Location</a></div>
                    <div class="breadcrumb-item">Create Location</div>
                </div>
            </div>

            <div class="section-body">


                <form action="{{ route('location.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Form Create</h4>
                                </div>
                                <div class="card-body">

                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">ServiceUnitID</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="ServiceUnitID">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">RoomID</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="RoomID">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">RoomCode</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="RoomCode">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Identifier
                                            Value (RoomID, RoomCode, ServiceUnitID)<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="identifier_value">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="name">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="select2" name="status">
                                                <option value="" disabled selected>-- Select item --</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}">{{ $status }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Pysical
                                            Type<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="select2" name="physical_type">
                                                <option value="" disabled selected>-- Select item --</option>
                                                @foreach ($physicalTypes as $type)
                                                    <option value="{{ $type['coding_code'] }}">{{ $type['coding_display'] }}
                                                        - ({{ $type['keterangan'] }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Location
                                            Mode<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="select2" name="location_mode">
                                                <option value="" disabled selected>-- Select item --</option>
                                                @foreach ($modes as $mode)
                                                    <option value="{{ $mode['mode'] }}">{{ $mode['mode'] }} -
                                                        ({{ $mode['keterangan'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Part Of</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="select2" name="part_of">
                                                <option value="" disabled selected>-- Select item --</option>
                                                @foreach ($locationByParts as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Organization</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="select2" name="organization_id">
                                                <option disabled selected>-- Select item --</option>
                                                @foreach ($organizations as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label
                                            class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description<code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <textarea class="form-control" name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    {{-- <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script> --}}

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

@endpush
