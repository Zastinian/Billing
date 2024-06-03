@extends('layouts.admin')

@inject('announcement_model', 'App\Models\Announcement')

@section('title', 'Announcements')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Announcements</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.announce.create') }}" class="btn btn-success btn-sm float-right">Create Announcement <i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="announcements-table" class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>Theme</th>
                                <th>Enabled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcement_model->all() as $announcement)
                                <tr>
                                    <td><a href="{{ route('admin.announce.show', ['id' => $announcement->id]) }}">{{ $announcement->id }}</a></td>
                                    <td>{{ $announcement->subject }}</td>
                                    <td>
                                        @switch($announcement->theme)
                                            @case(0)
                                                Success
                                                @break
                                            @case(1)
                                                Info
                                                @break
                                            @case(2)
                                                Warning
                                                @break
                                            @case(3)
                                                Danger
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($announcement->enabled)
                                            <i class="fas fa-check"></i> Yes
                                        @else
                                            <i class="fas fa-times"></i> No
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Theme</th>
                                <th>Enabled</th>
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
            $("#announcements-table").DataTable({"responsive": false, "lengthChange": false, "autoWidth": false});
        });
    </script>
@endsection
