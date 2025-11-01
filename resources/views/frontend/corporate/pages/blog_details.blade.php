@extends('frontend::pages.index')

@section('title')
    {{ $blog->title }}
@endsection

@section('page-content')
<section class="postbox-details-area position-relative fix section-space">
    <div class="container">
       <div class="row g-40">
          <div class="col-xxl-12">
             <div class="postbox-wrapper">
                <div class="postbox-item mb-55">
                   <div class="postbox-text">
                        {!! $blog->details !!}
                   </div>
                </div>

             </div>
          </div>
       </div>
    </div>
 </section>
@endsection
