@php $header_route = "admin.announce.index"; @endphp

@extends('layouts.admin')

@section('title', $announcement->subject)
@section('header', 'Announcements')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('api.admin.announce.update', ['id' => $id]) }}" method="PUT" data-callback="updateForm" id="updateForm">
                    @csrf

                    <div class="card-body row">
                        <div class="form-group col-8">
                            <label for="subjectInput">Subject</label>
                            <input type="text" name="subject" value="{{ $announcement->subject }}" class="form-control" id="subjectInput" placeholder="Subject" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="themeInput">Theme</label>
                            <select class="form-control" name="theme">
                                <option value="0" @if ($announcement->theme === 0) selected @endif>Success</option>
                                <option value="1" @if ($announcement->theme === 1) selected @endif>Info</option>
                                <option value="2" @if ($announcement->theme === 2) selected @endif>Warning</option>
                                <option value="3" @if ($announcement->theme === 3) selected @endif>Danger</option>
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label for="enabledInput">Enable</label>
                            <select class="form-control" name="enabled">
                                <option value="1" @if ($announcement->enabled) selected @endif>Yes</option>
                                <option value="0" @unless ($announcement->enabled) selected @endunless>No</option>
                            </select>
                        </div>
                        <div class="form-group col-12">
                            <label for="contentInput">Content</label>
                            <textarea type="text" name="content" class="form-control" id="contentInput" style="height:200px;" placeholder="Set 'Enable' to 'No' to save as draft" required>{{ $announcement->content }}</textarea>
                        </div>
                    </div>
                </form>
                <form action="{{ route('api.admin.announce.delete', ['id' => $id]) }}" method="DELETE" data-callback="deleteForm" id="deleteForm"></form>
                <div class="card-footer row justify-content-center">
                    <button type="submit" form="updateForm" class="btn btn-success btn-sm col-lg-2 col-3">Save</button>
                    <button type="submit" form="deleteForm" class="btn btn-danger btn-sm col-lg-2 col-3 offset-lg-1 offset-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
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
                waitRedirect('{{ route('admin.announce.index') }}')
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
