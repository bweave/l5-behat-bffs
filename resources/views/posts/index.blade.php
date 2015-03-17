@extends('app')

@section('content')

    <div class="container">
        <div class="page-header">
            <h1>Blog</h1>
        </div>

        <section class="posts">
            @foreach($posts as $post)
                <article class="post">
                    <h2>{{ $post->title }}</h2>
                    <div class="post-body">
                        {{ $post->body }}
                    </div>
                </article>
            @endforeach
        </section>
    </div>

@stop