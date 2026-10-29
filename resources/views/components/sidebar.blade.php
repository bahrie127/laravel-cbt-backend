<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}">CBT TPA</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('home') }}">TPA</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Ringkasan</li>
            <li class="{{ Request::routeIs('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            </li>
            <li class="{{ Request::routeIs('hasil.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('hasil.index') }}"><i class="fas fa-clipboard-check"></i><span>Hasil Ujian</span></a>
            </li>
            <li class="menu-header">Bank Soal & Materi</li>
            <li class="{{ Request::routeIs('soal.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('soal.index') }}"><i class="fas fa-list-ol"></i><span>Soal</span></a>
            </li>
            <li class="{{ Request::routeIs('materi.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('materi.index') }}"><i class="fas fa-book-open"></i><span>Materi</span></a>
            </li>
            <li class="{{ Request::routeIs('konten.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('konten.index') }}"><i class="fas fa-file-alt"></i><span>Konten App</span></a>
            </li>
            @can('kelola-pengguna')
                <li class="menu-header">Pengaturan</li>
                <li class="{{ Request::routeIs('user.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('user.index') }}"><i class="fas fa-users"></i><span>Pengguna</span></a>
                </li>
            @endcan
        </ul>
    </aside>
</div>
