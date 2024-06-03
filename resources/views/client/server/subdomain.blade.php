@extends('layouts.client')

@inject('server_model', 'App\Models\Server')
@inject('extension_manager', 'Extensions\ExtensionManager')

@section('title', 'Subdomain')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Subdomain Information</h5>
                </div>
                <div class="card-body text-nowrap row">
                    <p class="card-text col-5">
                        <b>Subdomain Name</b><br>
                        <b>Port Number</b>
                    </p>
                    <p class="card-text col-7">
                        @if ($server->subdomain_name)
                            {{ $server->subdomain_name }}
                        @else
                            Not Set
                        @endif<br>
                        @if ($server->subdomain_port)
                            {{ $server->subdomain_port }}
                        @else
                            Not Set
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 row">
            <div class="card col-12">
                <div class="card-header">
                    <h5 class="card-title m-0">Create/change your subdomain name</h5>
                </div>
                <div class="card-body text-nowrap">
                    <form action="{{ route('api.client.subdomain.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                        @csrf

                        <div class="input-group row">
                            <input type="text" name="name" class="form-control col-md-6" placeholder="Enter subdomain" required>
                            <select class="form-control col-md-6" name="subdomain">
                                @foreach ($extension_manager->subdomains as $extension)
                                    @foreach ($extension->getSubdomains() as $subdomain)
                                        <option value="{{ $extension }}:{{ $subdomain }}">.{{ $subdomain }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary">
                                Update Subdomain <i class="fas fa-arrow-circle-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('client_scripts')
    <script>
        function updateForm(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect(window.location.href)
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
    </script>
@endsection
