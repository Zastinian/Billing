@php $header_route = "admin.plan.index"; @endphp

@extends('layouts.admin')

@inject('category_model', 'App\Models\Category')
@inject('discount_model', 'App\Models\Discount')
@inject('coupon_model', 'App\Models\Coupon')
@inject('pterodactyl', 'App\Services\Pterodactyl')

@section('title', 'Create Server Plan')
@section('header', 'Server Plans')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.plan.create') }}" method="POST" data-callback="createForm">
                    @csrf
                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="nameInput">Server Plan Name</label>
                            <input type="text" name="name" class="form-control" id="nameInput" placeholder="Server Plan Name" required>
                        </div>
                        <div class="form-group col-lg-5">
                            <label for="descriptionInput">Short Description (Optional)</label>
                            <textarea name="description" class="form-control" id="descriptionInput" placeholder="Around 10 words recommended"></textarea>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="1000" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Category</label>
                            <select class="form-control" name="category">
                                @foreach ($category_model->orderBy('order', 'asc')->get() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="ramInput">RAM (MB)</label>
                            <input type="number" name="ram" min="0" class="form-control" id="ramInput" placeholder="0 = unlimited" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="cpuInput">CPU (%)</label>
                            <input type="number" name="cpu" min="0" class="form-control" id="cpuInput" placeholder="0 = unlimited" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="diskInput">Disk (MB)</label>
                            <input type="number" name="disk" min="0" class="form-control" id="diskInput" placeholder="0 = unlimited" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="swapInput">Swap (MB)</label>
                            <input type="number" name="swap" min="-1" class="form-control" id="swapInput" placeholder="0 = disable; -1 = unlimited" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="ioInput">Block IO</label>
                            <input type="number" name="io" min="10" max="1000" value="500" class="form-control" id="ioInput" placeholder="Within 10 and 1000" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="databasesInput">Databases</label>
                            <input type="number" name="databases" min="0" class="form-control" id="databasesInput" placeholder="Databases" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="backupsInput">Backups</label>
                            <input type="number" name="backups" min="0" class="form-control" id="backupsInput" placeholder="Backups" required>
                        </div>
                        <div class="col-lg-4 row">
                            <div class="form-group">
                                <label>Locations / Nodes</label>
                                @php $location_api = $pterodactyl->getLocations(); @endphp
                                @foreach ($location_api['data'] as $location)
                                    <br><label>{{ $location['attributes']['id'] }} - {{ $location['attributes']['long'] }} ({{ $location['attributes']['short'] }})</label>
                                    @foreach ($location['attributes']['relationships']['nodes']['data'] as $node)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="node[]" value="{{ $location['attributes']['id'] }}:{{ $node['attributes']['id'] }}">
                                            <p class="form-check-label">{{ $node['attributes']['name'] }}</p>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                            <div class="form-group">
                                <label>Nests / Eggs</label>
                                @php $nest_api = $pterodactyl->getNests(); @endphp
                                @foreach ($nest_api['data'] as $nest)
                                    <br><label>{{ $nest['attributes']['id'] }} - {{ $nest['attributes']['name'] }}</label>
                                    @foreach ($nest['attributes']['relationships']['eggs']['data'] as $egg)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="egg[]" value="{{ $nest['attributes']['id'] }}:{{ $egg['attributes']['id'] }}">
                                            <p class="form-check-label">{{ $egg['attributes']['name'] }}</p>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-8 row">
                            <div class="form-group col-lg-4">
                                <label for="extraPortsInput">Extra Ports</label>
                                <input type="number" name="extra_ports" min="0" class="form-control" id="extraPortsInput" placeholder="Extra Ports" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="minPortInput">Minimum Port (Optional)</label>
                                <input type="number" name="min_port" class="form-control" id="minPortInput" placeholder="e.g. 25565">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="maxPortInput">Maximum Port (Optional)</label>
                                <input type="number" name="max_port" class="form-control" id="maxPortInput" placeholder="e.g. 25575">
                            </div>
                            <div class="form-group col-lg-12">
                                <label for="serverDescriptionInput">Server Description (Optional)</label>
                                <textarea name="server_description" class="form-control" id="serverDescriptionInput" placeholder="Only displayed on Pterodactyl panel"></textarea>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Discount (Optional)</label>
                                <select class="form-control" name="discount">
                                    <option></option>
                                    @foreach ($discount_model->getValidDiscounts() as $discount)
                                        <option value="{{ $discount->id }}">{{ $discount->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Coupons (Optional)</label>
                                @foreach ($coupon_model->getValidCoupons() as $coupon)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="coupon[]" value="{{ $coupon->id }}">
                                        <p class="form-check-label">{{ $coupon->code }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="daysSuspendInput">Days before server suspension</label>
                                <input type="number" name="days_before_suspend" class="form-control" id="daysSuspendInput" placeholder="0 = Instantly suspended when overdue">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="daysSuspendInput">Days before server deletion</label>
                                <input type="number" name="days_before_delete" class="form-control" id="daysSuspendInput" placeholder="0 = Instantly deleted when overdue">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="globalLimitInput">Global Limit (Optional)</label>
                                <input type="number" name="global_limit" class="form-control" id="globalLimitInput" placeholder="0 = No servers can be created">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="perClientInput">Per Client Limit (Optional)</label>
                                <input type="number" name="per_client_limit" class="form-control" id="perClientInput" placeholder="0 = No servers can be created">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="perClientTrialInput">Per Client Trial Limit (Optional)</label>
                                <input type="number" name="per_client_trial_limit" class="form-control" id="perClientTrialInput" placeholder="0 = No free trials allowed">
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <hr>
                            <h5>Default Billing Cycle</h5>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Cycle Length</label>
                            <input type="number" name="cycle[0][cycle_length]" class="form-control" placeholder="e.g. enter '3', choose 'Monthly'" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Cycle Type</label>
                            <select class="form-control" name="cycle[0][cycle_type]">
                                <option value="0">One-time</option>
                                <option value="1">Hourly</option>
                                <option value="2">Daily</option>
                                <option value="3">Monthly</option>
                                <option value="4">Yearly</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Initial Price</label>
                            <input type="text" name="cycle[0][init_price]" class="form-control" placeholder="Use default currency" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Renewal Price</label>
                            <input type="text" name="cycle[0][renew_price]" class="form-control" placeholder="Use default currency" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Setup fee</label>
                            <input type="text" name="cycle[0][setup_fee]" class="form-control" placeholder="Use default currency" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Late fee</label>
                            <input type="text" name="cycle[0][late_fee]" class="form-control" placeholder="Use default currency" required>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Trial Length (Optional)</label>
                            <input type="number" name="cycle[0][trial_length]" min="0" class="form-control" placeholder="e.g. enter '7', choose 'Days'">
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Trial Type (Optional)</label>
                            <select class="form-control" name="cycle[0][trial_type]">
                                <option></option>
                                <option value="1">Hour(s)</option>
                                <option value="2">Day(s)</option>
                                <option value="3">Month(s)</option>
                                <option value="4">Year(s)</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-12">
                            <hr>
                            <h5>Additional Billing Cycles</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-cycle"><i class="fas fa-plus"></i> Add Billing Cycle</button>
                        </div>
                        <div class="col-lg-12 row" id="additional-cycles"></div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('admin.plan.index') }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function createForm(data) {
            if (data.success) {
                toastr.success(data.success)
                resetForms()
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }

        var i = 1

        $(document).on('click', '#add-cycle', function() {
            $('#additional-cycles').append(`
                <div class="row col-12">
                    <div class="form-group col-lg-3">
                        <label>Cycle Length</label>
                        <input type="number" name="cycle[${i}][cycle_length]" class="form-control" placeholder="e.g. enter '3', choose 'Monthly'">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Cycle Type</label>
                        <select class="form-control" name="cycle[${i}][cycle_type]">
                            <option value="0">One-time</option>
                            <option value="1">Hourly</option>
                            <option value="2">Daily</option>
                            <option value="3">Monthly</option>
                            <option value="4">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Initial Price</label>
                        <input type="text" name="cycle[${i}][init_price]" class="form-control" placeholder="Use default currency">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Renewal Price</label>
                        <input type="text" name="cycle[${i}][renew_price]" class="form-control" placeholder="Use default currency">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Setup fee</label>
                        <input type="text" name="cycle[${i}][setup_fee]" class="form-control" placeholder="Use default currency">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Late fee</label>
                        <input type="text" name="cycle[${i}][late_fee]" class="form-control" placeholder="Use default currency">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Trial Length (Optional)</label>
                        <input type="number" name="cycle[${i}][trial_length]" min="0" class="form-control" placeholder="e.g. enter '7', choose 'Days'">
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Trial Type (Optional)</label>
                        <select class="form-control" name="cycle[${i}][trial_type]">
                            <option></option>
                            <option value="1">Hour(s)</option>
                            <option value="2">Day(s)</option>
                            <option value="3">Month(s)</option>
                            <option value="4">Year(s)</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <button type="button" class="btn btn-danger btn-sm" onclick="return this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>
                        <hr>
                    </div>
                </div>
            `)

            i++
        })
    </script>
@endsection
