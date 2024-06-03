@php $header_route = "admin.kb.index"; @endphp

@extends('layouts.admin')

@inject('kb_category_model', 'App\Models\KbCategory')

@section('title', $article->subject)
@section('header', 'Knowledge Base')
@section('subheader', $kb_category_model->find($category_id)->value('name'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('api.admin.kb.article.update', ['category_id' => $category_id, 'article_id' => $article_id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-lg-6">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" value="{{ $article->subject }}" class="form-control" id="subjectInput" placeholder="Subject" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="{{ $article->order }}" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                        <div class="form-group col-12">
                            <textarea type="text" name="content" id="contentInput" placeholder="Article Content" style="height:200px;" required>{!! $article->content !!}</textarea>
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.kb.article.delete', ['category_id' => $category_id, 'article_id' => $article_id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer row justify-content-center">
                    <button type="submit" form="updateForm" class="btn btn-success btn-sm col-lg-2 col-3">Save</button>
                    <button type="submit" form="deleteForm" class="btn btn-danger btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/summernote/summernote-bs4.min.css'); </script>

    <script src="/plugins/summernote/summernote-bs4.min.js"></script>

    <script>$(function () { $('#contentInput').summernote() })</script>

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
