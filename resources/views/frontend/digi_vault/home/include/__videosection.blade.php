<!-- Video section start -->
<section class="td-video-section-start p-relative gray-bg z-index-11 section_space">
    <div class="container">
        <div class="row">
            <div class="col-xxl-8 col-xl-7 col-lg-7">
                <div class="video-thumb-box">
                    <div class="video-thumb" data-background="{{ asset($data['thumbnail_img']) }}">
                        <div class="play-video">
                            <a class="play-btn popup-video" href="{{ $data['video_link'] }}">
                                <span>
                                    <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.69435 20.1328C1.47406 20.1328 1.2628 20.0453 1.10704 19.8895C0.951277 19.7337 0.86377 19.5225 0.86377 19.3022V1.02965C0.863794 0.885319 0.901428 0.743487 0.972963 0.618133C1.0445 0.492779 1.14746 0.388228 1.27171 0.314787C1.39596 0.241346 1.5372 0.201548 1.68151 0.199318C1.82582 0.197088 1.96823 0.232501 2.09468 0.302068L18.7062 9.43835C18.8364 9.51004 18.945 9.61538 19.0207 9.74336C19.0963 9.87134 19.1362 10.0173 19.1362 10.1659C19.1362 10.3146 19.0963 10.4605 19.0207 10.5885C18.945 10.7165 18.8364 10.8218 18.7062 10.8935L2.09468 20.0298C1.97205 20.0973 1.83434 20.1327 1.69435 20.1328Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-5 col-lg-5">
                <div class="video-contents has_fade_anim">
                    <div class="section-title-wrapper">
                        <span class="section-subtitle">{{ $data['small_title'] }}</span>
                        <h2 class="section-title mb-30">{{ $data['big_title'] }}</h2>
                        <div class="btn-inner">
                            <a class="td-btn gradient-btn radius-8" href="{{ $data['video_link'] }}">
                                <span class="btn-icon">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_268_14279)">
                                            <path
                                                d="M15.2387 0.761321C14.5127 0.0346544 13.4634 -0.191346 12.4814 0.178654L1.55206 4.70799C0.261393 5.19399 -0.137274 6.39865 0.0407264 7.37599C0.219393 8.35466 1.01673 9.34132 2.39673 9.34132H6.64939V13.604C6.64939 14.9833 7.63606 15.7813 8.61406 15.9587C8.76006 15.9853 8.91073 15.9993 9.06339 15.9993C9.93606 15.9993 10.8687 15.55 11.2761 14.4693L15.8294 3.49799C16.1921 2.53665 15.9661 1.48732 15.2387 0.761321ZM14.5894 3.00732L10.0361 13.9787C9.80539 14.5887 9.27273 14.724 8.85339 14.648C8.43206 14.5713 7.98206 14.2567 7.98206 13.6047V8.67532C7.98206 8.30665 7.68406 8.00865 7.31539 8.00865H2.39673C1.74406 8.00865 1.42939 7.55866 1.35273 7.13732C1.27673 6.71666 1.41206 6.18465 2.04273 5.94732L12.9721 1.41799C13.1227 1.36132 13.2754 1.33332 13.4254 1.33332C13.7461 1.33332 14.0534 1.46065 14.2967 1.70399C14.6534 2.06065 14.7594 2.55599 14.5894 3.00732Z"
                                                fill="white" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_268_14279">
                                                <rect width="16" height="16" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Video section end -->
