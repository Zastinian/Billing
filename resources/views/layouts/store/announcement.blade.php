@foreach (config('announcement.items') as $announcement)
    @if ($announcement['enabled'])
        @switch($announcement['theme'])
            @case(0)
                <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i>
                @break
            @case(1)
                <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i>
                @break
            @case(2)
                <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i>
                @break
            @case(3)
                <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i>
                @break
            @default
                <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i>
        @endswitch
        &nbsp;{{ $announcement['subject']}}</h5>
        {{ $announcement['content'] }}
        </div>
    @endif
@endforeach
