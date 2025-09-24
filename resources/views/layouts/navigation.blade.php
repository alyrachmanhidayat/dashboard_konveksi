{{-- Navbar dan Sidebar
<nav class="navbar align-items-start p-0 sidebar sidebar-dark accordion bg-gradient-primary navbar-dark">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand d-flex justify-content-center align-items-center m-0 sidebar-brand" href="{{ route('dashboard') }}">
            <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
            <div class="mx-3 sidebar-brand-text"><span>Brand</span></div>
        </a>
        <hr class="my-0 sidebar-divider">
        <ul class="navbar-nav text-light" id="accordionSidebar">
            <li class="nav-item">
                <a class="nav-link active" style="text-align: center;">USER</a>
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="spk-1.html"><i class="far fa-edit"></i><span>SPK</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" style="text-align: center;"><strong>ADMIN</strong></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="spk-close.html"><i class="fas fa-table"></i><span>SPK Closed</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="invoice.html"><i class="fas fa-table"></i><span>Invoice</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="piutang.html"><i class="fas fa-table"></i><span>Piutang</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="rekap-omzet.html"><i class="fas fa-table"></i><span>Rekap Omzet</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="rekap-reject.html"><i class="fas fa-table"></i><span>Rekap Reject</span></a>
            </li>
        </ul>
        <div class="text-center d-none d-md-inline">
            <button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button>
        </div>
    </div>
</nav> --}}