<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Vsure CRM')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- sort function -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @stack('maps-script')
    @stack('map-loader')

    <style>
        body { overflow-x: hidden; }

        .kanban-card {
          cursor: move;
        }

        .kanban-placeholder {
          background: #f8f9fa;
          border: 2px dashed #E34234;
          height: 60px;
          margin-bottom: 8px;
          border-radius: 4px;
        }

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
            <img src="{{ asset('assets/img/vsure.png') }}" height="50" class="me-2">
            Vsure CRM
        </a>

        <div class="d-flex align-items-center">
          <!-- <span class="me-4">
            <i class="fa-solid fa-file-circle-plus"></i>
            Brochure
            <span class="badge bg-warning text-dark">0</span>
          </span> -->
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
            <div class="bg-light text-dark rounded p-1" style="min-height:92vh;">
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
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        });
    }

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
if (!window.mdb) {
    window.mdb = {};
}

if (!window.mdb.Datatable) {
    window.mdb.Datatable = class {
        constructor(element, config = {}) {
            this.element = element;
            this.columns = config.columns || [];
            this.render([]);
        }

        update(payload = {}) {
            this.render(payload.rows || []);
        }

        render(rows) {
            const head = this.columns.map((column) => `<th>${column.label}</th>`).join('');
            const body = rows.map((row) => {
                const cells = this.columns.map((column) => `<td>${row[column.field] ?? ''}</td>`).join('');
                return `<tr>${cells}</tr>`;
            }).join('');

            this.element.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>${head}</tr>
                        </thead>
                        <tbody>${body}</tbody>
                    </table>
                </div>
            `;
        }
    };
}
</script>

<!-- Page-specific JS (MOVED HERE – IMPORTANT) -->
@stack('page-js')
@stack('modal-js')

</body>
</html>
