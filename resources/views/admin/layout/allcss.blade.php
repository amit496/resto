<script src="{{ asset('admin/assets/js/theme-script.js') }}"></script>

<link rel="shortcut icon" type="image/svg+xml" href="{{ asset('admin/assets/img/favicon.svg') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('admin/assets/img/favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/animate.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
<style>
    :root {
        --bs-primary: #cb202d;
        --bs-primary-rgb: 203, 32, 45;
        --foodi-accent: #cb202d;
        --foodi-accent-dark: #9f1823;
        --foodi-yellow: #f4b400;
        --foodi-green: #1ba672;
        --foodi-charcoal: #2d2d2d;
        --foodi-soft: #fff5f5;
    }
    body,
    .main-wrapper {
        background: linear-gradient(135deg, #fff8f7 0%, #fffaf1 52%, #f6fbf9 100%);
    }
    .card,
    .modal-content,
    .module-filter-card {
        border-radius: 16px;
        border-color: rgba(203, 32, 45, 0.10);
        box-shadow: 0 16px 38px rgba(26, 26, 26, 0.06);
    }
    .header,
    .sidebar {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
    }
    .header {
        border-bottom: 1px solid rgba(203, 32, 45, 0.08);
    }
    .sidebar {
        border-right: 1px solid rgba(203, 32, 45, 0.06);
    }
    .module-filter-card {
        background: #fff;
        border: 1px solid rgba(203, 32, 45, 0.10);
        padding: 12px;
    }
    .module-filter-card .form-control,
    .module-filter-card .select2-container .select2-selection--single {
        min-height: 38px;
    }
    .module-filter-actions {
        display: flex;
        gap: 8px;
    }
    .sidebar .sidebar-menu ul li.active > a,
    .sidebar .sidebar-menu ul li a.active {
        background: rgba(203, 32, 45, 0.10);
        color: var(--foodi-accent);
        border-radius: 12px;
        font-weight: 700;
    }
    .sidebar .sidebar-menu ul li a i,
    .sidebar .sidebar-menu ul li a img {
        filter: saturate(1.1);
    }
    .btn-primary,
    .btn-primary:hover,
    .btn-primary:focus {
        background: var(--foodi-accent);
        border-color: var(--foodi-accent);
    }
    .btn-outline-primary {
        color: var(--foodi-accent);
        border-color: rgba(203, 32, 45, 0.32);
    }
    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background: var(--foodi-accent);
        border-color: var(--foodi-accent);
    }
    .btn-outline-warning {
        color: #b78300;
        border-color: rgba(244, 180, 0, 0.46);
    }
    .btn-outline-success {
        color: var(--foodi-green);
        border-color: rgba(27, 166, 114, 0.34);
    }
    .page-wrapper .content h3,
    .page-wrapper .content h5,
    .page-wrapper .content h6 {
        color: var(--foodi-charcoal);
    }
    .sidebar .sidebar-menu ul li a:hover {
        color: var(--foodi-accent);
    }
    .table thead th {
        background: rgba(203, 32, 45, 0.06);
        color: var(--foodi-charcoal);
    }
    .table tbody tr:hover {
        background: rgba(203, 32, 45, 0.02);
    }
    .form-control:focus,
    .form-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: rgba(203, 32, 45, 0.36) !important;
        box-shadow: 0 0 0 0.2rem rgba(203, 32, 45, 0.10) !important;
    }
    .badge.bg-primary,
    .bg-primary {
        background: var(--foodi-accent) !important;
    }
    .alert-success {
        background: rgba(27, 166, 114, 0.10);
        border-color: rgba(27, 166, 114, 0.18);
        color: var(--foodi-green);
    }
    .alert-danger {
        background: rgba(203, 32, 45, 0.08);
        border-color: rgba(203, 32, 45, 0.18);
        color: var(--foodi-accent);
    }
    .badge,
    .pill {
        border-radius: 999px;
    }
    #global-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    #global-loader .whirly-loader {
        display: none !important;
    }
    .foodihub-loader-wrap {
        position: relative;
        width: 72px;
        height: 72px;
        display: grid;
        place-items: center;
    }
    .foodihub-loader-logo {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        position: relative;
        z-index: 2;
        background: #fff;
    }
    .foodihub-loader-ring {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        border: 5px solid rgba(244, 180, 0, 0.24);
        border-top-color: var(--foodi-accent);
        animation: foodihub-spin 0.8s linear infinite;
    }
    @keyframes foodihub-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

