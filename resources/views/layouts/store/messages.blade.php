@if ($errors->any())
    <div class="alert alert-danger">
        Please fix the following error(s):
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif (session('danger_msg'))
    <div class="alert alert-danger">
        {!! session('danger_msg') !!}
    </div>
@elseif (session('warning_msg'))
    <div class="alert alert-warning">
       {!! session('warning_msg') !!}
    </div>
@elseif (session('info_msg'))
    <div class="alert alert-info">
        {!! session('info_msg') !!}
    </div>
@elseif (session('success_msg'))
    <div class="alert alert-success">
        {!! session('success_msg') !!}
    </div>
@endif
