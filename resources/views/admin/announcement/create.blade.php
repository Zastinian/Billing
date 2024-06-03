@php $header_route = "admin.announce.index"; @endphp

@extends('layouts.admin')

@section('title', 'Create Announcement')
@section('header', 'Announcements')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('api.admin.announce.create') }}" method="POST" data-callback="createForm">
                @csrf

                <div class="card">
                    <div class="card-body row">
                        <div class="form-group col-8">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" class="form-control" id="subjectInput" placeholder="Subject" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="themeInput">Theme</label>
                            <select class="form-control" name="theme">
                                <option value="0">Success</option>
                                <option value="1">Info</option>
                                <option value="2">Warning</option>
                                <option value="3">Danger</option>
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label for="enabledInput">Enable</label>
                            <select class="form-control" name="enabled">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label for="contentInput">Content</label>
                            <textarea type="text" name="content" class="form-control" id="contentInput" style="height:200px;" placeholder="Set 'Enable' to 'No' to save as draft" required></textarea>
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <a href="{{ route('admin.announce.index') }}" class="btn btn-default btn-sm col-lg-2 col-3">Cancel</a>
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('admin_scripts')
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
