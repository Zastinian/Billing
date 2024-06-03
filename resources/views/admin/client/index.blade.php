@extends('layouts.admin')

@inject('plan_model', 'App\Models\Plan')

@section('title', 'Clients')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <form action="" method="POST">
                            @csrf
                            
                            <button type="submit" class="btn btn-success btn-sm">One-click Import Pterodactyl Users <i class="fas fa-plus-circle"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="clients-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Panel ID</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>Admin</th>
                                <th>Credit</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td><a href="{{ route('admin.client.show', ['id' => $client->id]) }}">{{ $client->id }}</a></td>
                                    <td><a href="{{ config('app.panel_url') }}/admin/users/view/{{ $client->user_id }}" target="_blank">{{ $client->user_id }}</a></td>
                                    <td>{{ $client->email }}</td>
                                    <td>
                                        @if ($client->email_verified_at)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>
                                        @if ($client->is_admin)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                    <td>{!! session('currency')->symbol !!}{{ number_format($client->credit * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                    <td>{{ $client->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Panel ID</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>Admin</th>
                                <th>Credit</th>
                                <th>Registration Date</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); </script>

    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function () {
            $("#clients-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
