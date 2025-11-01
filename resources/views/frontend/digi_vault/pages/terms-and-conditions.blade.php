@extends('frontend::pages.index')
@section('title')
    {{ $data['title'] }}
@endsection
@section('meta_keywords')
    {{ $data['meta_keywords'] }}
@endsection
@section('meta_description')
    {{ $data['meta_description'] }}
@endsection
@section('page-content')
    <!-- Terms conditions section start -->
    <section class="td-privacy-policy inner-pages_space-top section_space-bottom">
        <div class="container">
            <div class="td-page-contents has_fade_anim">
                {!! $data['content'] !!}
            </div>
        </div>
    </section>
    <!-- Terms conditions section end -->
@endsection
