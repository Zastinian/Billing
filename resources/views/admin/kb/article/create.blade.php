@php $header_route = "admin.kb.index"; @endphp

@extends('layouts.admin')

@inject('kb_category_model', 'App\Models\KbCategory')

@section('title', 'Create Support Article')
@section('header', 'Knowledge Base')
@section('subheader', $kb_category_model->find($category_id)->value('name'))

@section('content')
    <form action="{{ route('api.admin.kb.article.create', ['category_id' => $category_id]) }}" method="POST" data-callback="createForm">
        @csrf

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body row">
                        <div class="form-group col-lg-6">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subjectInput" placeholder="Subject" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="orderInput">Order (smaller = higher display priority)</label>
                            <input type="text" name="order" value="1000" class="form-control" id="orderInput" placeholder="Order" required>
                        </div>
                        <div class="alert alert-warning">
                            If you have changed the HTML in the 'Code View', make sure you exit it and preview the page before saving.
                        </div>
                        <div class="form-group col-12">
                            <textarea type="text" name="content" id="contentInput" placeholder="Article Content" style="height:200px;" required>{!! old('content') !!}</textarea>
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('admin.kb.show', ['category_id' => $category_id]) }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/summernote/summernote-bs4.min.css'); </script>

    <script src="/plugins/summernote/summernote-bs4.min.js"></script>

    <script>$(function () { $('#contentInput').summernote() })</script>

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
    </script>
@endsection
