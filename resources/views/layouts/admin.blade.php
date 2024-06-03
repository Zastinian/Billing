<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@yield('title') | Admin Area - {{ config('app.company_name') }}</title>
        @include('layouts.styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed @if(config('app.dark_mode')) dark-mode @endif">
        <div class="wrapper">
            <!-- Preloader -->
            @include('layouts.preloader')
            
            <!-- Navbar -->
            @include('layouts.admin.nav')

            <!-- Main Sidebar Container -->
            @include('layouts.admin.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                @include('layouts.admin.header')

                <!-- Main content -->
                <div class="content">
                    <div class="container-fluid">
                        @include('layouts.admin.alerts')
                        @include('layouts.store.alerts')
                        @include('layouts.store.announcement')
                        @include('layouts.store.messages')
                        @yield('content')
                    </div>
                </div>
            </div>

            <!-- Main Footer -->
            @include('layouts.client.footer')
        </div>

        @include('layouts.scripts')
        @include('layouts.admin.scripts')
    </body>
</html>
