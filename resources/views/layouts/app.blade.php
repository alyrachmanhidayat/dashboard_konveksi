<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bss-overrides.css') }}">

</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="{{ url('/') }}">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>Dashboard<br>Konveksi</span></div>
                </a>
                <hr class="sidebar-divider my-0">

                <ul class="navbar-nav text-light" id="accordionSidebar">
                    <!-- Menu untuk Semua User (Admin & User Biasa) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('spk*') ? 'active' : '' }}" href="{{ url('/spk') }}">
                            <i class="fas fa-file-invoice"></i><span>SPK</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('spkClose-view') ? 'active' : '' }}" href="{{ route('spk.closed.view') }}">
                            <i class="fas fa-file-invoice"></i><span>SPK Closed</span>
                        </a>
                    </li>

                    <!-- =============================================================== -->
                    <!-- Menu ini HANYA akan muncul untuk Admin yang sudah login -->
                    <!-- =============================================================== -->
                    @auth
                    @if(Auth::user()->is_admin)
                    <hr class="sidebar-divider">
                    <div class="sidebar-heading">
                        ADMIN AREA
                    </div>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('spk-close') ? 'active' : '' }}" href="{{ url('/spk-close') }}">
                            <i class="fas fa-file-signature"></i><span>SPK Closed Admin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('invoice') ? 'active' : '' }}" href="{{ url('/invoice') }}">
                            <i class="fas fa-money-check"></i><span>Invoice</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('piutang') ? 'active' : '' }}" href="{{ url('/piutang') }}">
                            <i class="fas fa-hand-holding-usd"></i><span>Piutang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('rekap-omzet') ? 'active' : '' }}" href="{{ url('/rekap-omzet') }}">
                            <i class="fas fa-chart-bar"></i><span>Rekap Omzet</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('rekap-reject') ? 'active' : '' }}" href="{{ url('/rekap-reject') }}">
                            <i class="fas fa-chart-line"></i><span>Rekap Reject</span>
                        </a>
                    </li>
                    @endif
                    @endauth
                    <!-- =============================================================== -->

                </ul>

                <div class="text-center d-none d-md-inline">
                    <button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button>
                </div>
            </div>
        </nav>
        <!-- End of Sidebar -->

        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            @guest
                            <!-- Login button for guests -->
                            <li class="nav-item my-auto">
                                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                            @endguest

                            @auth
                            <div class="d-none d-sm-block topbar-divider"></div>
                            @endauth

                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow">
                                    <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                                        @auth
                                        <!-- Jika sudah login, tampilkan nama user -->
                                        <span class="d-none d-lg-inline me-2 text-gray-600 small">{{ Auth::user()->name }}</span>
                                        @else
                                        <!-- Jika belum login, tampilkan sebagai guest -->
                                        <span class="d-none d-lg-inline me-2 text-gray-600 small">Guest User</span>
                                        @endauth
                                        <img class="border rounded-circle img-profile" src="https://via.placeholder.com/60">
                                    </a>

                                    <!-- Dropdown untuk User -->
                                    @auth
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a>
                                        <div class="dropdown-divider"></div>

                                        <!-- Logout Form -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout
                                            </a>
                                        </form>
                                    </div>
                                    @endauth
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- Footer -->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © SISNET 2025</span></div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SB Admin core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <!-- Performance optimization: defer loading of non-critical JavaScript -->
    <script>
        // Simple performance optimization for sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-toggled');
                    document.querySelector('.sidebar').classList.toggle('toggled');
                });
            }
        });
    </script>

    <!-- (Opsional) Livereload untuk development -->
    @env('local')
    <script src="{{ asset('js/livereload.js') }}"></script>
    @endenv
    @stack('scripts')
</body>

</html>