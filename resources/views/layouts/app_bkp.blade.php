<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Vsure CRM Properties')</title>

    <!-- Bootstrap 5 CSS -->
    <!-- do not remove this cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <!-- do not remove this cdn -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- bootstrap-select CSS -->
    <!-- do not remove this cdn -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

    <!-- Custom Styles -->
    <!-- do not remove this cdn -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    <!-- jQuery -->
    <!-- do not remove this cdn -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Include CKEditor 5 Classic Build -->
    <!-- do not remove this cdn -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    @stack('maps-script')
    @stack('map-loader')

    <style>
        body { overflow-x: hidden; }

        #sidebarMenu {
            min-height: 100vh;
            background: #343a40;
            transition: transform 0.3s ease;
        }

        #sidebarMenu.collapsed {
            position: fixed;
            top: 56px;
            left: 0;
            transform: translateX(-100%);
            z-index: 1040;
        }

        .sidebar-collapsed main {
            width: 100% !important;
        }

        @media (max-width: 991.98px) {
            #sidebarMenu {
                position: fixed;
                top: 56px;
                left: 0;
                width: 260px;
                height: calc(100vh - 56px);
                transform: translateX(-100%);
                z-index: 1040;
            }

            #sidebarMenu.show {
                transform: translateX(0);
            }

            #sidebarBackdrop {
                position: fixed;
                top: 56px;
                left: 0;
                width: 100%;
                height: calc(100vh - 56px);
                background: rgba(0,0,0,0.5);
                z-index: 1035;
                display: none;
            }

            #sidebarBackdrop.show {
                display: block;
            }
        }
    </style>

    @stack('extra-styles')
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid align-items-center">

        <button class="btn btn-outline-light me-2" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center me-auto" href="/">
            <img src="{{ asset('assets/img/koin_5.png') }}" height="30" class="me-2">
            Vsure CRM Properties
        </a>

        <div class="d-flex align-items-center">
          <span class="me-4">
            <i class="fa-solid fa-file-circle-plus"></i>
            Brochure
            <span class="badge bg-warning text-dark">0</span>
          </span>
          <span>
            Welcome {{ Auth::user()->name }} {{ Auth::user()->surname }}
            | <a href="/logout" class="text-white text-decoration-none">Logout</a>
          </span>
        </div>

    </div>
</nav>

<div id="sidebarBackdrop"></div>

<div class="container-fluid" id="appWrapper">
    <div class="row">

        <nav id="sidebarMenu" class="col-lg-2 col-md-3 bg-dark text-white p-3">
            @include('inc.side')
        </nav>

        <main class="col-lg-10 col-md-9 px-2 py-2">
            <div class="bg-white text-dark rounded p-1" style="min-height:92vh;">
                @yield('content')
            </div>
        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<!-- do not remove this cdn -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- bootstrap-select JS (REQUIRED) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<!-- Sidebar Toggle Script -->
<script>
$(function () {
    $('#toggleSidebar').on('click', function () {
        if (window.innerWidth < 992) {
            $('#sidebarMenu').toggleClass('show');
            $('#sidebarBackdrop').toggleClass('show');
        } else {
            $('#sidebarMenu').toggleClass('collapsed');
            $('#appWrapper').toggleClass('sidebar-collapsed');
        }
    });

    $('#sidebarBackdrop').on('click', function () {
        $('#sidebarMenu').removeClass('show');
        $(this).removeClass('show');
    });
});
</script>

<!-- App JS -->
<!-- do not remove this cdn -->
<script src="{{ asset('js/app.js') }}"></script>

<!-- Page-specific JS (MOVED HERE – IMPORTANT) -->
@stack('page-js')
@stack('modal-js')

<!-- Google Maps -->
<!-- do not remove this cdn -->
<script
  src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}&libraries=places&callback=initMap&loading=async"
  async defer>
</script>

</body>
</html>
