@if (session('notify'))
    @php
        $notification = session('notify');
        $icon = $notification['type'] == 'success' ? 'check' : 'alert-triangle';
    @endphp
    <!-- single alert-box -->
    @if($notification['type'] == 'success')
        <div class="alert-show-status">
            <div class="td-alert-box has-success">
                <div class="alert-content">
                    <span class="alert-icon">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 10.0857V11.0057C20.9988 13.1621 20.3005 15.2604 19.0093 16.9875C17.7182 18.7147 15.9033 19.9782 13.8354 20.5896C11.7674 21.201 9.55726 21.1276 7.53447 20.3803C5.51168 19.633 3.78465 18.2518 2.61096 16.4428C1.43727 14.6338 0.879791 12.4938 1.02168 10.342C1.16356 8.19029 1.99721 6.14205 3.39828 4.5028C4.79935 2.86354 6.69279 1.72111 8.79619 1.24587C10.8996 0.770634 13.1003 0.988061 15.07 1.86572"
                                stroke="#48B16E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21 3.00574L11 13.0157L8 10.0157" stroke="#48B16E" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <div class="contents">
                        <h6 class="alert-title">{{ ucfirst($notification['title']) }}</h6>
                        <p class="alert-message">{{ $notification['message'] }}</p>
                    </div>
                </div>
                <button class="close-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4.00004L4 14M3.99996 4L13.9999 14" stroke="#979FA9" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>
    @elseif($notification['type'] == 'error')
        <div class="alert-show-status">
            <div class="td-alert-box has-success">
                <div class="alert-content">
                    <span class="alert-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="6" fill="url(#paint0_linear_344_491)" />
                            <path d="M15 9.00002L9 15M8.99997 9L14.9999 15" stroke="white" stroke-width="1.5"
                                stroke-linecap="round" />
                            <defs>
                                <linearGradient id="paint0_linear_344_491" x1="12" y1="0" x2="12" y2="24"
                                    gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#E88B76" />
                                    <stop offset="1" stop-color="#CA5048" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </span>
                    <div class="contents">
                        <h6 class="alert-title">{{ ucfirst($notification['title']) }}</h6>
                        <p class="alert-message">{{ $notification['message'] }}</p>
                    </div>
                </div>
                <button class="close-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4.00004L4 14M3.99996 4L13.9999 14" stroke="#979FA9" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
@endif
@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="alert-show-status">
        <div class="td-alert-box has-success">
            <div class="alert-content">
                <span class="alert-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" rx="6" fill="url(#paint0_linear_344_491)" />
                        <path d="M15 9.00002L9 15M8.99997 9L14.9999 15" stroke="white" stroke-width="1.5"
                            stroke-linecap="round" />
                        <defs>
                            <linearGradient id="paint0_linear_344_491" x1="12" y1="0" x2="12" y2="24"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#E88B76" />
                                <stop offset="1" stop-color="#CA5048" />
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <div class="contents">
                    <h6 class="alert-title">{{ __('Validation Error') }}</h6>
                    <p class="alert-message">{{ $error }}</p>
                </div>
            </div>
            <button class="close-btn">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 4.00004L4 14M3.99996 4L13.9999 14" stroke="#979FA9" stroke-width="1.5"
                        stroke-linecap="round" />
                </svg>
            </button>
        </div>
    </div>
    @endforeach
    
@endif