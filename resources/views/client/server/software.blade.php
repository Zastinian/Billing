@extends('layouts.client')

@inject('extension_manager', 'Extensions\ExtensionManager')

@section('title', 'Softwares')

@section('content')
    <form action="{{ route('api.client.software.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
        @csrf

        <div class="row justify-content-center">
            @foreach ($extension_manager->softwares as $extension)
                <div class="col-lg-5">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $extension::$display_name }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                @foreach ($extension::softwares() as $software => $versions)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="software" value="{{ $extension::$display_name }}:{{ $software }}">
                                        <label class="form-check-label">{{ $software }}</label>
                                        <select name="version" class="form-control">
                                            @foreach ($versions as $version)
                                                <option value="{{ $version }}">{{ $version }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-12 row justify-content-center mb-2">
                <button type="submit" class="btn btn-primary">
                    Install Software <i class="fa fa-download"></i>
                </button>
            </div>
        </div>
    </form>
@endsection

@section('client_scripts')
    <script>
        function updateForm(data) {
            if (data.success) {
                toastr.success(data.success)
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
