@extends('layouts.admin')

@inject('kb_category_model', 'App\Models\KbCategory')
@inject('article_model', 'App\Models\KbArticle')

@section('title', 'Knowledge Base')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Knowledge Base Categories</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.kb.create') }}" class="btn btn-success btn-sm float-right">Create Category <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="kb-categories-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Articles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kb_category_model->all() as $category)
                                <tr>
                                    <td>{{ $category->order }}</td>
                                    <td><a href="{{ route('admin.kb.show', ['category_id' => $category->id]) }}">{{ $category->id }}</a></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $article_model->where('category_id', $category->id)->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Articles</th>
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
            $("#kb-categories-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
