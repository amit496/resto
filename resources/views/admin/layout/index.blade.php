<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">
@include('admin.layout.head')

<body>
    <div id="global-loader">
        <div class="foodihub-loader-wrap">
            <img src="{{ asset('admin/assets/img/loader.svg') }}" alt="Foodihub Loader" class="foodihub-loader-logo">
            <div class="foodihub-loader-ring"></div>
        </div>
        <div class="text-center mt-2 small fw-semibold">Foodihub</div>
    </div>

    <div class="main-wrapper">
        @include('admin.layout.header')
        @include('admin.layout.left-sidebar')

        <div class="page-wrapper">
            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @include('admin.layout.alljs')
    @stack('script')
</body>

</html>

