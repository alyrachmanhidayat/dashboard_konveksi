<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bss-overrides.css') }}">
    <style>
        #wrapper {
            display: flex;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            /* Membuat sidebar setinggi layar */
        }

        #content-wrapper {
            height: 100vh;
            overflow-y: auto;
            /* Membuat hanya area konten yang bisa di-scroll */
            width: 100%;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            /* Memastikan topbar selalu di atas konten */
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
            <div class="container-fluid d-flex flex-column p-0">
                <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="{{ url('/') }}">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-tshirt"></i></div>
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
                    <!-- password change modal -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">
                            <i class="fas fa-cog"></i><span>Ganti Password</span>
                        </a>
                    </li>
                    @endif
                    @endauth
                    <!-- =============================================================== -->

                </ul>

                <div class="text-center d-none d-md-inline mt-auto mb-3">
                    <button class="btn rounded-circle border-0" id="sidebarToggle" type="button">
                    </button>
                </div>
            </div>
        </nav>

        <!-- Modal Button -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Ganti Password</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label for="current_password" class="form-label text-dark">Password Lama</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan password lama" required>
                                <div class="invalid-feedback" id="current_password_error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label text-dark">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Masukkan password baru" required>
                                <div class="form-text">Password minimal 8 karakter dengan kombinasi huruf dan angka.</div>
                                <div class="invalid-feedback" id="new_password_error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label text-dark">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru" required>
                                <div class="invalid-feedback" id="new_password_confirmation_error"></div>
                            </div>
                        </form>
                        <hr>
                        <p>Pastikan password baru sesuai dan anda mengingatnya.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" id="changePasswordBtn">Simpan Password</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Sidebar -->

        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
                    <div class="container-fluid">
                        <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            @auth
                            <!-- Show live clock and date -->
                            <li class="nav-item me-3">
                                <div id="live-clock" class="d-none d-lg-block">
                                    <span id="date" class="text-gray-600 small"></span>
                                    <span id="time" class="text-gray-800 fw-bold small ms-2"></span>
                                </div>
                            </li>
                            <!-- Show user name when authenticated -->
                            <li class="nav-item">
                                <span class="d-none d-lg-inline me-2 text-gray-600 small">{{ Auth::user()->name }}</span>
                            </li>
                            <li class="nav-item my-auto me-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </button>
                                </form>
                            </li>
                            @endauth

                            @guest
                            <!-- Show live clock and date for guests -->
                            <li class="nav-item me-3">
                                <div id="live-clock" class="d-none d-lg-block">
                                    <span id="date" class="text-gray-600 small"></span>
                                    <span id="time" class="text-gray-800 fw-bold small ms-2"></span>
                                </div>
                            </li>
                            <!-- Login button for guests -->
                            <li class="nav-item my-auto me-3">
                                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                            @endguest
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
                    <div class="text-center my-auto copyright"><span>Copyright © 2025</span></div>
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
    <!-- list JS -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>

    <script>
        // Live clock functionality
        function updateClock() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };

            const dateElement = document.getElementById('date');
            const timeElement = document.getElementById('time');

            if (dateElement && timeElement) {
                dateElement.textContent = now.toLocaleDateString('id-ID', dateOptions); // Menggunakan locale Indonesia
                timeElement.textContent = now.toLocaleTimeString('id-ID', timeOptions);
            }
        }

        // Initialize the clock and update every second
        updateClock();
        setInterval(updateClock, 1000);
    </script>

    <!-- Password Change JavaScript -->
    <script>
        $(document).ready(function() {
            // Handle password change button click
            $('#changePasswordBtn').on('click', function() {
                // Reset error messages
                $('#current_password_error').text('').removeClass('d-block');
                $('#new_password_error').text('').removeClass('d-block');
                $('#new_password_confirmation_error').text('').removeClass('d-block');

                // Remove invalid class from inputs
                $('#current_password, #new_password, #new_password_confirmation').removeClass('is-invalid');

                // Hide any existing success message
                $('#passwordChangeSuccessMessage').remove();

                // Get form data
                const formData = {
                    current_password: $('#current_password').val(),
                    new_password: $('#new_password').val(),
                    new_password_confirmation: $('#new_password_confirmation').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Validate passwords match
                if (formData.new_password !== formData.new_password_confirmation) {
                    $('#new_password_confirmation_error').text('Password baru dan konfirmasi password tidak cocok.').addClass('d-block');
                    $('#new_password_confirmation').addClass('is-invalid');
                    return;
                }

                // Validate password length
                if (formData.new_password.length < 8) {
                    $('#new_password_error').text('Password baru harus minimal 8 karakter.').addClass('d-block');
                    $('#new_password').addClass('is-invalid');
                    return;
                }

                // Disable button and show loading state
                $(this).prop('disabled', true).text('Memproses...');

                // Make AJAX request
                $.ajax({
                    url: '{{ route("user.change-password") }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Create success message element
                        const successMessage = $('<div id="passwordChangeSuccessMessage" class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                            '<i class="fas fa-check-circle me-2"></i>' +
                            (response.message || 'Password berhasil diubah!') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');

                        // Insert success message before the form
                        $('#changePasswordForm').before(successMessage);

                        // Reset form
                        $('#changePasswordForm')[0].reset();

                        // Reset button
                        $('#changePasswordBtn').prop('disabled', false).text('Simpan Password');

                        // Auto-hide the modal after 2 seconds to give user time to see the success message
                        setTimeout(function() {
                            $('#exampleModal').modal('hide');

                            // Remove the success message after modal is closed
                            $('#passwordChangeSuccessMessage').remove();
                        }, 2000);
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            if (errors.current_password) {
                                $('#current_password_error').text(errors.current_password[0]).addClass('d-block');
                                $('#current_password').addClass('is-invalid');
                            }

                            if (errors.new_password) {
                                $('#new_password_error').text(errors.new_password[0]).addClass('d-block');
                                $('#new_password').addClass('is-invalid');
                            }

                            if (errors.new_password_confirmation) {
                                $('#new_password_confirmation_error').text(errors.new_password_confirmation[0]).addClass('d-block');
                                $('#new_password_confirmation').addClass('is-invalid');
                            }
                        } else if (xhr.status === 401) {
                            // Current password is incorrect
                            $('#current_password_error').text('Password lama tidak benar.').addClass('d-block');
                            $('#current_password').addClass('is-invalid');
                        } else {
                            // General error
                            // Create error message for general error
                            const errorMessage = $('<div id="passwordChangeErrorMessage" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' +
                                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                                'Terjadi kesalahan saat mengganti password. Silakan coba lagi.' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');

                            // Insert error message before the form
                            $('#changePasswordForm').before(errorMessage);
                        }

                        // Reset button
                        $('#changePasswordBtn').prop('disabled', false).text('Simpan Password');
                    }
                });
            });

            // Clear error messages when user types
            $('#current_password, #new_password, #new_password_confirmation').on('input', function() {
                const fieldId = $(this).attr('id');
                $('#' + fieldId + '_error').text('').removeClass('d-block');
                $(this).removeClass('is-invalid');

                // Remove success message if user starts typing again
                $('#passwordChangeSuccessMessage').remove();
                $('#passwordChangeErrorMessage').remove();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>