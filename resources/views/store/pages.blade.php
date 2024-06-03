@extends('layouts.store')

@switch(Route::currentRouteName())
    @case('home')
        @section('title', 'Home')
        @break
    @case('status')
        @section('title', 'System Status')
        @break
    @case('terms')
        @section('title', 'Terms of Service')
        @break
    @case('privacy')
        @section('title', 'Privacy Policy')
        @break
    @default
@endswitch

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {!! config('page.' . Route::currentRouteName()) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
