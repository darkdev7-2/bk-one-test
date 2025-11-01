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
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                {!! $data['content'] !!}
            </div>
        </div>
    </div>
@endsection
