<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">@yield('title')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('client.dash') }}">Client Area</a></li>
                    @hasSection('header') <li class="breadcrumb-item"><a href="{{ route($header_route) }}">@yield('header')</a></li> @endif
                    <li class="breadcrumb-item active">@hasSection('subheader') @yield('subheader') @else @yield('title') @endif</li>
                </ol>
            </div>
        </div>
    </div>
</div>
