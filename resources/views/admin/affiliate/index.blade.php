@extends('layouts.admin')

@inject('affiliate_model', 'App\Models\AffiliateEarning')
@inject('client_model', 'App\Models\Client')

@section('title', 'Affiliates')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="affiliates-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Referer</th>
                                <th>Buyer</th>
                                <th>Product</th>
                                <th>Commission</th>
                                <th>Conversion</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($affiliate_model->all() as $affiliate)
                                <tr>
                                    <td>{{ $affiliate->id }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $affiliate->client_id]) }}" target="_blank">{{ $client_model->find($affiliate->client_id)->email }}</a></td>
                                    <td><a href="{{ route('admin.client.show', ['id' => $affiliate->buyer_id]) }}" target="_blank">{{ $client_model->find($affiliate->buyer_id)->email }}</a></td>
                                    <td>{{ $affiliate->product }}</td>
                                    <td>{!! session('currency')->symbol !!}{{ number_format($affiliate->commission * session('currency')->rate, 2) }} {{ session('currency')->name }}</td>
                                    <td>{{ $affiliate->conversion }}%</td>
                                    <td>
                                        @switch($affiliate->status)
                                            @case(0)
                                                <span class="badge bg-success">Accepted</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-danger">Rejected</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $affiliate->created_at }}</td>
                                    <td>
                                        @if ($affiliate->status === 1)
                                            <form action="{{ route('api.admin.affiliate.accept', ['id' => $$affiliate->id]) }}" method="POST" data-callback="actionForm">
                                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
                                            </form>
                                            <form action="{{ route('api.admin.affiliate.reject', ['id' => $$affiliate->id]) }}" method="POST" data-callback="actionForm">
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Referer</th>
                                <th>Buyer</th>
                                <th>Product</th>
                                <th>Commission</th>
                                <th>Conversion</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
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
            $("#affiliates-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>

    <script>
        function actionForm(data) {
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
