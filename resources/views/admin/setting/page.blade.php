@extends('layouts.admin')

@inject('page_model', 'App\Models\Page')

@switch($name)
    @case('home')
        @section('title', 'Edit Home Page')
        @break
    @case('status')
        @section('title', 'Edit System Status Page')
        @break
    @case('terms')
        @section('title', 'Edit Terms of Service Page')
        @break
    @case('privacy')
        @section('title', 'Edit Privacy Policy Page')
        @break
    @default
@endswitch

@section('content')
    <form action="{{ route('api.admin.setting.page') }}" method="PUT" data-callback="pageForm">
        @csrf

        <input type="hidden" name="name" value="{{ $name }}" required>

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    If you have changed the HTML in the 'Code View', make sure you exit it and preview the page before saving.
                </div>
                <div class="card">
                    <div class="card-body row">
                        <div class="form-group col-12">
                            <textarea type="text" name="content" id="contentInput" placeholder="Page Body" style="height:200px;">{!! $page_model->where('name', $name)->value('content') !!}</textarea>
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-md-4">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('admin_scripts')
    <script> lazyLoadCss('/plugins/summernote/summernote-bs4.min.css'); </script>

    <script src="/plugins/summernote/summernote-bs4.min.js"></script>
    
    <script>
        $(function () { $('#contentInput').summernote() })

        function pageForm(data) {
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
