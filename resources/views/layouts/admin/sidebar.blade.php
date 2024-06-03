@inject('extension_manager', 'Extensions\ExtensionManager')

<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dash') }}" class="brand-link">
    <img src="{{ config('app.logo_file_path') }}" alt="{{ config('app.company_name') }} Logo" class="brand-image">
    <span class="brand-text font-weight-light">{{ config('app.company_name') }}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-legacy nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                    with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('admin.dash') }}" class="nav-link @if(Route::currentRouteName() == 'admin.dash') active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Admin Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="fas fa-server nav-icon"></i>
                        <p>
                            Servers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.servers.active') }}" class="nav-link @if(Route::currentRouteName() == 'admin.servers.active') active @endif">
                                <i class="nav-icon far fa-check-circle"></i>
                                <p>Active</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.servers.pending') }}" class="nav-link @if(Route::currentRouteName() == 'admin.servers.pending') active @endif">
                                <i class="nav-icon fas fa-ellipsis-h"></i>
                                <p>Pending</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.servers.suspended') }}" class="nav-link @if(Route::currentRouteName() == 'admin.servers.suspended') active @endif">
                                <i class="nav-icon far fa-times-circle"></i>
                                <p>Suspended</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.servers.canceled') }}" class="nav-link @if(Route::currentRouteName() == 'admin.servers.canceled') active @endif">
                                <i class="nav-icon fas fa-ban"></i>
                                <p>Canceled</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">CLIENTS</li>
                <li class="nav-item">
                    <a href="{{ route('admin.client.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.client.index') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clients</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="fas fa-user-friends nav-icon"></i>
                        <p>
                            Affiliate Program
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.affiliate.show') }}" class="nav-link @if(Route::currentRouteName() == 'admin.affiliate.show') active @endif">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.affiliate.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.affiliate.index') active @endif">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Affiliates</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">PLANS AND PRICING</li>
                <li class="nav-item">
                    <a href="{{ route('admin.plan.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.plan.index') active @endif">
                        <i class="nav-icon fas fa-scroll"></i>
                        <p>Server Plans</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.category.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.category.index') active @endif">
                        <i class="nav-icon far fa-folder"></i>
                        <p>Server Categories</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.addon.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.addon.index') active @endif">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                        <p>Plan Add-ons</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.discount.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.discount.index') active @endif">
                        <i class="nav-icon fas fa-percent"></i>
                        <p>Discounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.coupon.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.coupon.index') active @endif">
                        <i class="nav-icon fas fa-tag"></i>
                        <p>Coupon Codes</p>
                    </a>
                </li>
                <li class="nav-header">BILLING</li>
                <li class="nav-item">
                    <a href="{{ route('admin.income') }}" class="nav-link @if(Route::currentRouteName() == 'admin.income') active @endif">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Income</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.invoice.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.invoice.index') active @endif">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Invoices</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.currency.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.currency.index') active @endif">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>Currencies</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.tax.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.tax.index') active @endif">
                        <i class="nav-icon fas fa-funnel-dollar"></i>
                        <p>Taxes</p>
                    </a>
                </li>
                <li class="nav-header">SUPPORT</li>
                <li class="nav-item">
                    <a href="{{ route('admin.ticket.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.ticket.index') active @endif">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Support Tickets</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.kb.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.kb.index') active @endif">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Knowledge Base</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.announce.index') }}" class="nav-link @if(Route::currentRouteName() == 'admin.announcement.index') active @endif">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Announcements</p>
                    </a>
                </li>
                <li class="nav-header">SETTINGS</li>
                <li class="nav-item">
                    <a href="{{ route('admin.setting.show') }}" class="nav-link @if(Route::currentRouteName() == 'admin.setting.show') active @endif">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Store Settings</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="fas fa-file nav-icon"></i>
                        <p>
                            Store Pages
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.page.show', ['name' => 'home']) }}" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Home Page</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.page.contact') }}" class="nav-link">
                                <i class="nav-icon fas fa-at"></i>
                                <p>Contact Form</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.page.show', ['name' => 'terms']) }}" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Terms of Service</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.page.show', ['name' => 'privacy']) }}" class="nav-link">
                                <i class="nav-icon far fa-file-alt"></i>
                                <p>Privacy Policy</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.page.show', ['name' => 'status']) }}" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Status Page</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">EXTENSIONS</li>
                @foreach ($extension_manager->getAllExtensionsWithSettings() as $extension)
                    <li class="nav-item">
                        <a href="{{ route('admin.extension.show', ['id' => $extension::$display_name]) }}" class="nav-link">
                            <i class="nav-icon fas fa-plug"></i>
                            <p>{{ $extension::$display_name }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
