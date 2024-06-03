@php $header_route = "admin.kb.index"; @endphp

@extends('layouts.admin')

@inject('article_model', 'App\Models\KbArticle')

@section('title', $category->name)
@section('header', 'Knowledge Base')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.kb.update', ['category_id' => $category_id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-6">
                            <label for="nameInput">Category Name</label>
                            <input type="text" name="name" value="{{ $category->name }}" class="form-control" id="nameInput" placeholder="Category Name" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="{{ $category->order }}" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.kb.delete', ['category_id' => $category_id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer col-lg-12 row justify-content-center">
                    <button type="submit" form="updateForm" class="btn btn-success btn-sm col-lg-2 col-3">Save</button>
                    <button type="submit" form="deleteForm" class="btn btn-danger btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Support Articles in this category</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.kb.article.create', ['category_id' => $category_id]) }}" class="btn btn-success btn-sm float-right">Create Support Article <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="kb-articles-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($article_model->where('category_id', $category_id)->get() as $article)
                                <tr>
                                    <td>{{ $article->order }}</td>
                                    <td><a href="{{ route('admin.kb.article.show', ['category_id' => $category_id, 'article_id' => $article->id]) }}">{{ $article->id }}</a></td>
                                    <td>{{ $article->subject }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Order</th>
                                <th>ID</th>
                                <th>Subject</th>
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
            $("#kb-articles-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>

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
        
        function deleteForm(data) {
            if (data.success) {
                toastr.success(data.success)
                waitRedirect('{{ route('admin.kb.index') }}')
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
