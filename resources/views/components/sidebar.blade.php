<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="">
                <img src="{{ asset('img/logo.png') }}" width="30" alt="">
                SIFA-SATUSEHAT
                <img src="{{ asset('img/SatuSehat.png') }}" width="30" alt="">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="">Sifa-SatuSehat</a>
        </div>

        <ul class="sidebar-menu">
            <li class=' {{ env('IS_PROD') == true ? 'bg-success' : 'bg-warning' }}'>
                <a class="nav-link" href="javascript:void()"><i class="fas fa-rocket"></i> Mode
                    {{ env('IS_PROD') == true ? 'PRODUCTION' : 'SANBOX' }}</a>
            </li>

            <li class="menu-header">Menu </li>
            <li class='{{ Request::is('dashboard') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ url('dashboard') }}"><i class="fas fa-fire"></i> Dashboard</a>
            </li>
            {{-- @if (auth()->user()->role == 'pendaftaran') --}}
            {{-- <li class="menu-header">Pendaftaran</li>
                <li class="{{ Request::is('md/patient*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('patient.index') }}"><i class="fas fa-user"></i> Patient
                        GeneralConsent</a>
                </li>
                <li class="{{ Request::is('pendaftaran/patient-gc*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('pendaftaran/patient-gc') }}"><i class="fas fa-user"></i> Patient
                        GeneralConsent</a>
                </li> --}}
            {{-- @endif --}}
            @if (auth()->user()->role == 'admin')
                <li class="nav-item dropdown {{ Request::is('md*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i>
                        <span>Master Data</span></a>
                    <ul class="dropdown-menu">

                        <li class="{{ Request::is('md/patient*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('patient.index') }}">Patient</a>
                        </li>

                        <li class="{{ Request::is('md/dokter*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dokter.index') }}">Practitioner</a>
                        </li>
                        <li class="{{ Request::is('md/organization*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('organization.index') }}">Organization</a>
                        </li>
                        <li class="{{ Request::is('md/location*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('location.index') }}">Location</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown {{ Request::is('encounter*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-columns"></i>
                        <span>Encounter Bundle</span></a>
                    <ul class="dropdown-menu">
                        {{-- <li class="{{ Request::is('kj/pendaftaran') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pendaftaran.index') }}">TES GET ENCOUNTER ID</a>
                    </li> --}}
                        <li class="{{ Request::is('encounter/bundle/rajal') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('encounter/bundle/rajal', []) }}">Rawat Jalan</a>
                        </li>
                        <li class="{{ Request::is('encounter/bundle/ranap') ? 'active' : '' }}">
                            <a class="nav-link text-danger" href="{{ url('encounter/bundle/ranap', []) }}">Rawat
                                Inap</a>
                        </li>
                        <li class="{{ Request::is('encounter/bundle/igd') ? 'active' : '' }}">
                            <a class="nav-link text-danger" href="{{ url('encounter/bundle/igd', []) }}">IGD</a>
                        </li>
                    </ul>
                </li>

                <li class="menu-header">manage</li>
                <li class="nav-item dropdown {{ Request::is('mu**') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-tasks"></i><span>Manage User</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('mu/user') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('user.index') }}">User</a>
                        </li>
                        {{-- <li class="{{ Request::is('mu/role') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('role.index') }}">Role</a>
                    </li>
                    <li class="{{ Request::is('mu/permission') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('permission.index') }}">Permission</a>
                    </li>
                    <li class="{{ Request::is('mu/role-permission') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('role-permission.index') }}">Role Permission</a>
                    </li> --}}
                    </ul>
                </li>
                {{-- <li class="nav-item dropdown {{ Request::is('mp*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa-solid fa-group-arrows-rotate ml-1"></i> <span>Mappings</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('mp/encounter*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('mapping.encounter.index')}}">Encounter</a>
                    </li>
                </ul>
            </li> --}}
            @endif
            <li class="menu-header">Docs</li>
            <li class="nav-item dropdown  {{ Request::is('dc*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                        class="fas fa-rocket"></i><span>Documentation</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('dc/docs-location') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('docs.location') }}">Location</a>
                    </li>
                    <li class="{{ Request::is('dc/docs-organization') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('docs.organization') }}">Organization</a>
                    </li>
                    <li class="{{ Request::is('dc/docs-encounter') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('docs.encounter') }}">Encounter</a>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="hide-sidebar-mini mt-4 mb-4 p-3">
            <a href="{{ route('documentation.index') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Documentation
            </a>
        </div>
    </aside>
</div>
