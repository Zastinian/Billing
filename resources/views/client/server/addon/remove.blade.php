@extends('layouts.client')

@inject('addon_model', 'App\Models\Addon')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-warning row justify-content-center">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Are you sure to remove <b>{{ $addon_model->find($addon_id)->value('name') }}</b>?</h5>
                Your add-on will be removed immediately without any refund.
                <button type="submit" form="removeAddon" class="btn btn-danger col-md-4">Yes</button>
                <a href="{{ route('client.server.addon.show', ['id' => $server->id]) }}" class="btn btn-default col-md-4 offset-md-1">No</a>
            </div>
        </div>
    </div>
    <form action="" method="POST" id="removeAddon"> @csrf </form>
@endsection