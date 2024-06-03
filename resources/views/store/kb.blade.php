@extends('layouts.store')

@inject('kb_category_model', 'App\Models\KbCategory')
@inject('kb_article_model', 'App\Models\KbArticle')

@section('title', 'Knowledge Base')

@section('content')
    <div class="row justify-content-center">
        @foreach ($kb_category_model->orderBy('order', 'asc')->get() as $kb_category)
            <div class="col-lg-5">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $kb_category->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach ($kb_article_model->where('category_id', $kb_category->id)->orderBy('order', 'asc')->get() as $kb_article)
                                <li><a href="{{ route('kb', ['id' => $kb_article->id]) }}">{{ $kb_article->subject }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
