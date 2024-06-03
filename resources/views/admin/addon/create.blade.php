@php $header_route = "admin.addon.index"; @endphp

@extends('layouts.admin')

@inject('category_model', 'App\Models\Category')

@section('title', 'Create Plan Add-on')
@section('header', 'Plan Add-ons')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.addon.create') }}" method="POST" data-callback="createForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-4">
                            <label for="nameInput">Add-on Name</label>
                            <input type="text" name="name" class="form-control" id="nameInput" placeholder="Add-on Name" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="descriptionInput">Short Description (Optional)</label>
                            <textarea name="description" class="form-control" id="descriptionInput" placeholder="Around 10 words recommended"></textarea>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="1000" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label>Categories</label>
                            @foreach ($category_model->all() as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category[]" value="{{ $category->id }}">
                                    <p class="form-check-label">{{ $category->name }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Resource</label>
                            <select class="form-control" name="resource">
                                <option value="ram">RAM (MB)</option>
                                <option value="cpu">CPU (%)</option>
                                <option value="disk">Disk (MB)</option>
                                <option value="database">Database</option>
                                <option value="backup">Backup</option>
                                <option value="extra_port">Extra Port</option>
                                <option value="dedicated_ip">Dedicated IP ^</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-5">
                            <label for="amountInput">Additional Amount // ^Comma-separated IP List</label>
                            <input type="text" name="amount" class="form-control" id="amountInput" placeholder="e.g. 1.2.3.4,1.2.3.5,1.2.3.6">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="globalLimitInput">Global Limit (Optional)</label>
                            <input type="number" name="global_limit" min="0" class="form-control" id="globalLimitInput" placeholder="0 = No servers can use this add-on">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="perClientInput">Per Client Limit (Optional)</label>
                            <input type="number" name="per_client_limit" min="0" class="form-control" id="perClientInput" placeholder="0 = No servers can use this add-on">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="perServerInput">Per Server Limit (Optional)</label>
                            <input type="number" name="per_server_limit" min="0" class="form-control" id="perServerInput" placeholder="Dedicated IP: enter 1">
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
                        <div class="form-group col-lg-12">
                            <hr>
                            <h5>Additional Billing Cycles</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-cycle"><i class="fas fa-plus"></i> Add Billing Cycle</button>
                        </div>
                        <div class="col-lg-12 row" id="additional-cycles"></div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('admin.addon.index') }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
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
                        <button type="button" class="btn btn-danger btn-sm" onclick="return this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="form-group col-lg-12">
                        <hr>
                    </div>
                </div>
            `)

            i++
        })
    </script>
@endsection
