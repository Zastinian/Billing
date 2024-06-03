@extends('layouts.store')

@inject('kb_article_model', 'App\Models\KbArticle')

@section('title', 'Knowledge Base')

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Related Support Articles</h5>
                    <p class="card-text">
                        @foreach ($kb_article_model->where('category_id', $article->category_id)->orderBy('order', 'asc')->get() as $kb_article)
                            @if ($kb_article->id !== $article->id)
                                <li><a href="{{ route('kb', ['id' => $kb_article->id]) }}">{{ $kb_article->subject }}</a></li>
                            @endif
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h1>{{ $article->subject }}</h1>
                    <p><a href="{{ route('kb') }}" class="card-link"><i class="fas fa-arrow-left text-sm"></i> Back to Knowledge Base</a></p>
                    {!! $article->content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
