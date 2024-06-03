@inject('plan_model', 'App\Models\Plan')
@inject('category_model', 'App\Models\Category')
@inject('kb_category_model', 'App\Models\KbCategory')
@inject('currency_model', 'App\Models\Currency')
@inject('tax_model', 'App\Models\Tax')

<nav class="main-header navbar navbar-expand-md @if(config('app.dark_mode')) navbar-dark @else navbar-white navbar-light @endif">
    <div class="container">
        <a {!! to_page('home') !!} class="navbar-brand">
        <img src="{{ config('app.logo_file_path') }}" alt="{{ config('app.company_name') }} Logo" height="50px">
        <span class="brand-text font-weight-light">{{ config('app.company_name') }}</span>
        </a>
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                @if ($category_model->exists()) 
                    <li class="nav-item dropdown">
                        <a id="plansMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Plans</a>
                        <ul aria-labelledby="plansMenu" class="dropdown-menu border-0 shadow">
                            <li><a {!! to_page('plans') !!} class="dropdown-item">All Plans</a></li>
                            <div class="dropdown-divider"></div>
                            @foreach ($category_model->orderBy('order', 'asc')->get() as $category)
                                <li><a {!! to_page('plans', ['id' => $category->id]) !!} class="dropdown-item">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @if (config('page.contact')) <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link">Contact Us</a></li> @endif
                @if ($kb_category_model->exists()) <li class="nav-item"><a {!! to_page('kb') !!} class="nav-link">Support</a></li> @endif
            </ul>
        </div>
        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <li class="nav-item dropdown">
                <a id="currencyMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{ session('currency')->name }}<a>
                <ul aria-labelledby="currencyMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                    @foreach ($currency_model->all() as $currency)
                        <li><a href="{{ route('currency', ['id' => $currency->id]) }}" class="dropdown-item">{{ $currency->name }}</a></li>
                    @endforeach
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a id="countryMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                    @if (session('tax')->country === '0')
                        Global
                    @else
                        {{ session('tax')->country }}
                    @endif
                <a>
                <ul aria-labelledby="countryMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                    @foreach ($tax_model->all() as $tax)
                        <li><a href="{{ route('country', ['id' => $tax->id]) }}" class="dropdown-item">{{ $tax->country }}</a></li>
                    @endforeach
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a id="countryMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">English<a>
                <ul aria-labelledby="countryMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                    <li><a href="{{ route('lang', ['id' => 'EN']) }}" class="dropdown-item">English</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                @if (auth()->check())
                    <a id="accountMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{ auth()->user()->email }}</a>
                    <ul aria-labelledby="accountMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                        <li><a href="{{ route('client.dash') }}" class="dropdown-item">Client Area</a></li>
                        <li><a href="{{ route('client.credit.show') }}" class="dropdown-item">Account Credit</a></li>
                        <li><a href="{{ route('client.account.show') }}" class="dropdown-item">Account Settings</a></li>
                        @if (auth()->user()->is_admin)
                            <li><a href="{{ route('admin.dash') }}" class="dropdown-item">Admin Area</a></li>
                        @endif
                        <div class="dropdown-divider"></div>
                        <li><a href="{{ route('client.logout') }}" class="dropdown-item">Logout</a></li>
                    </ul>
                @else
                    <a id="accountMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Account</a>
                    <ul aria-labelledby="accountMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                        <li><a href="javascript:void(0);" data-toggle="modal" data-target="#login-modal" class="dropdown-item">Login</a></li>
                        @if (config('app.open_registration')) <li><a href="javascript:void(0);" data-toggle="modal" data-target="#register-modal" class="dropdown-item">Register</a></li> @endif
                        <div class="dropdown-divider"></div>
                        <li><a href="javascript:void(0);" data-toggle="modal" data-target="#forgot-modal" class="dropdown-item">Forgot Password?</a></li>
                    </ul>
                @endif
            </li>
        </ul>
    </div>
</nav>
