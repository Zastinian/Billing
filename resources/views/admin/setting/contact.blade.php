@extends('layouts.admin')

@inject('page_model', 'App\Models\Page')
@inject('contact_model', 'App\Models\Contact')

@section('title', 'View/Edit Contact Form')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('api.admin.setting.page') }}" method="PUT" data-callback="pageForm">
                @csrf

                <input type="hidden" name="name" value="contact" required>
    
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="receiverInput">Email Address for Receiving New Message Notification (Leave blank to disable the contact form)</label>
                            <input type="email" name="content" value="{{ $page_model->where('name', 'contact')->value('content') }}" class="form-control" id="receiverInput" placeholder="Leave this blank to disable the contact form">
                        </div>
                    </div>
                    <div class="card-footer row justify-content-center">
                        <button type="submit" class="btn btn-success btn-sm col-lg-2 col-md-4">Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contact Form Messages</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="messages-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width:5%">ID</th>
                                <th style="width:25%">Email</th>
                                <th style="width:20%">Name</th>
                                <th style="width:30%">Subject</th>
                                <th style="width:20%">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contact_model->all() as $message)
                                <tr>
                                    <td><a href="{{ route('admin.page.message', ['msg_id' => $message->id]) }}">{{ $message->id }}</a></td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ $message->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="width:5%">ID</th>
                                <th style="width:25%">Email</th>
                                <th style="width:20%">Name</th>
                                <th style="width:30%">Subject</th>
                                <th style="width:20%">Date</th>
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
            $("#messages-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });

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
