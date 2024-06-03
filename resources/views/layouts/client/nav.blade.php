@inject('plan_model', 'App\Models\Plan')
@inject('category_model', 'App\Models\Category')
@inject('kb_category_model', 'App\Models\KbCategory')

<nav class="main-header navbar navbar-expand @if(config('app.dark_mode')) navbar-dark @else navbar-white navbar-light @endif">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item dropdown">
            <a id="storeMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle">Store</a>
            <ul aria-labelledby="storeMenu" class="dropdown-menu border-0 shadow">
                <li><a href="{{ route('home') }}" class="dropdown-item">Home</a></li>
                <div class="dropdown-divider"></div>
                @if ($category_model->exists())
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="plansMenu" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Plans</a>
                        <ul aria-labelledby="plansMenu" class="dropdown-menu border-0 shadow">
                            <li><a href="{{ route('plans') }}" class="dropdown-item">View All Plans</a></li>
                            <div class="dropdown-divider"></div>
                            @foreach ($category_model->orderBy('order', 'asc')->get() as $category)
                                <li><a href="{{ route('plans', ['id' => $category->id]) }}" class="dropdown-item">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @if (config('page.contact')) <li><a href="{{ route('contact') }}"  class="dropdown-item">Contact Us</a></li> @endif
                @if ($kb_category_model->exists()) <li><a href="{{ route('kb') }}" class="dropdown-item">Knowledge Base</a></li> @endif
                @if (config('page.status')) <li><a href="{{ route('status') }}" class="dropdown-item">System Status</a></li> @endif
            </ul>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a id="accountMenu" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{ auth()->user()->email }}</a>
            <ul aria-labelledby="accountMenu" class="dropdown-menu dropdown-menu-right border-0 shadow">
                <li><a href="{{ route('client.credit.show') }}" class="dropdown-item">Account Credit</a></li>
                <li><a href="{{ route('client.account.show') }}" class="dropdown-item">Account Settings</a></li>
                @if (auth()->user()->is_admin)
                    <li><a href="{{ route('admin.dash') }}" class="dropdown-item">Admin Area</a></li>
                @endif
                <div class="dropdown-divider"></div>
                <li><a href="{{ route('client.logout') }}" class="dropdown-item">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
