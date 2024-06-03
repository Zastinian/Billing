@extends('layouts.admin')

@inject('currency_model', 'App\Models\Currency')

@section('title', 'Currencies')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Currencies</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.currency.create') }}" class="btn btn-success btn-sm float-right">Create Currency <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="currencies-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Symbol</th>
                                <th>Conversion Rate</th>
                                <th>Precision</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($currency_model->all() as $currency)
                                <tr>
                                    <td><a href="{{ route('admin.currency.show', ['id' => $currency->id]) }}">{{ $currency->id }}</a></td>
                                    <td>{{ $currency->name }}</td>
                                    <td>{!! $currency->symbol !!}</td>
                                    <td>{{ $currency->rate }}</td>
                                    <td>{{ $currency->precision }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Symbol</th>
                                <th>Conversion Rate</th>
                                <th>Precision</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="callout callout-info">
                <h5>Default currency</h5>
                <hr>
                <h3>
                    {{ $currency_model->where('default', true)->value('name') }} 
                    ( {!! $currency_model->where('default', true)->value('symbol') !!} )
                </h3>
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
            $("#currencies-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
