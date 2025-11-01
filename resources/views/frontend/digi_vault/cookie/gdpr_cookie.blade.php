
@if(setting('gdpr_status','gdpr') == true)
<!-- Cookie notes start -->
<div class="caches-privacy cookiealert" hidden>
    <div class="caches-contents">
        <h4 class="title">{{ __('Cookie Settings') }}</h4>
        <p>{{ setting('gdpr_text','gdpr') }}</p>
    </div>
    <div class="caches-btns">
        <a class="td-btn gradient-outline-btn btn-h-40 radius-8" target="_blank" href="{{ url(setting('gdpr_button_url','gdpr')) }}">
            <span class="btn-text">{{setting('gdpr_button_label','gdpr')}}</span>
        </a>
        <button type="button" class="td-btn gradient-btn acceptcookies btn-h-40 radius-8">
            <span class="btn-text">{{ __('Accept All') }}</span>
        </button>
    </div>
</div>
<!-- Cookie notes end -->
@endif