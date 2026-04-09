<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name') }} admin panel for restaurant operations and user management.">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>

    @include('admin.layout.allcss')
    @stack('styles')

</head>

