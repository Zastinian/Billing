<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@yield('title') - {{ config('app.company_name') }}</title>
        @include('layouts.styles')
    </head>
    <body class="hold-transition layout-top-nav @if(config('app.dark_mode')) dark-mode @endif">
        <div class="wrapper">
            <!-- Preloader -->
            @include('layouts.preloader')

            <!-- Navbar -->
            @include('layouts.store.nav')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" id="page-content">
                <!-- Content Header (Page header) -->
                @include('layouts.store.header')

                <!-- Main content -->
                <div class="content">
                    <div class="container" id="content-container">
                        @include('layouts.store.alerts')
                        @include('layouts.store.announcement')
                        @include('layouts.store.messages')
                        @yield('content')
                    </div>
                    
                    @include('layouts.store.modals')
                </div>
            </div>

            <!-- Main Footer -->
            @include('layouts.store.footer')
        </div>

        @include('layouts.scripts')
        @include('layouts.store.scripts')
    </body>
</html>
