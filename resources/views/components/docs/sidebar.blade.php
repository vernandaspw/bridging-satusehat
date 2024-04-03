<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="">
                <img src="{{ asset('img/logo.png') }}" width="30" alt="">
                {{ env('APP_BRAND') }}
                <img src="{{ asset('img/SatuSehat.png') }}" width="30" alt="">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="">{{ env('APP_BRAND') }}</a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Mulai</li>
            <li class='{{ Request::is('docs') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ url('docs') }}"><i class="fas fa-fire"></i> Perkenalan</a>
            </li>
            <li class='{{ Request::is('docs/>instalasi') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ url('docs/instalasi') }}"><i class="fas fa-fire"></i> Instalasi</a>
            </li>
            <li class="menu-header">Api Service Docs</li>
            <li class='{{ Request::is('docs/>patient') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ url('docs/patient') }}"><i class="fas fa-fire"></i> Patient</a>
            </li>
            <li class='{{ Request::is('docs/>dokter') ? 'active' : '' }}'>
                <a class="nav-link" href="{{ url('docs/dokter') }}"><i class="fas fa-fire"></i> Dokter</a>
            </li>

        </ul>

    </aside>
</div>
