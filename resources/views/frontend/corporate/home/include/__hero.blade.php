<section class="banner-area banner-style-two p-relative include-bg fix" data-background="{{ asset('front/theme-2') }}/images/banner/banner-bg.png">
    <div class="container">
       <div class="row gy-50 align-items-center">
          <div class="col-xxl-6 col-xl-6 col-lg-6">
             <div class="banner-content">
                <span class="banner-subtitle">
                   <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M9 18.5C10.1819 18.5 11.3522 18.2672 12.4442 17.8149C13.5361 17.3626 14.5282 16.6997 15.364 15.864C16.1997 15.0282 16.8626 14.0361 17.3149 12.9442C17.7672 11.8522 18 10.6819 18 9.5C18 8.3181 17.7672 7.14778 17.3149 6.05585C16.8626 4.96392 16.1997 3.97177 15.364 3.13604C14.5282 2.30031 13.5361 1.63738 12.4442 1.18508C11.3522 0.732792 10.1819 0.5 9 0.5C6.61305 0.5 4.32387 1.44821 2.63604 3.13604C0.948212 4.82387 0 7.11305 0 9.5C0 11.8869 0.948212 14.1761 2.63604 15.864C4.32387 17.5518 6.61305 18.5 9 18.5ZM8.768 13.14L13.768 7.14L12.232 5.86L7.932 11.019L5.707 8.793L4.293 10.207L7.293 13.207L8.067 13.981L8.768 13.14Z" fill="white"/>
                   </svg>
                   {{ $data['sub_title'] }}
                </span>
                <h1 class="banner-title">  {{ $data['hero_title'] }} <span class="mark-line-inner gd-text">{{ $data['hero_colour_full_title'] }} <img class="mark-line" src="{{ asset('front/theme-2') }}/images/banner/mark-line.svg" alt="mark-line"></span></h1>
                {{-- Static Content --}}
                <div class="banner-list">
                   <ul>
                      <li>
                         <div class="list-item">
                            <span class="list-icon"><img src="{{ asset('front/theme-2') }}/images/icons/check-white.svg" alt="check"></span>
                            <span class="list-title">{{ __('Our platform is fast') }}</span>
                         </div>
                         <div class="list-item">
                            <span class="list-icon"><img src="{{ asset('front/theme-2') }}/images/icons/check-white.svg" alt="check"></span>
                            <span class="list-title">{{ __('We keep your data secure') }}</span>
                         </div>
                         <div class="list-item">
                            <span class="list-icon"><img src="{{ asset('front/theme-2') }}/images/icons/check-white.svg" alt="check"></span>
                            <span class="list-title">{{ __('Enjoy hassle-free use') }}</span>
                         </div>
                      </li>
                   </ul>
                </div>
                <div class="btn-wrap">
                   <a class="site-btn gdt-btn" href="{{ $data['hero_button1_url'] }}" target="{{ $data['hero_button1_target'] }}"> {{ $data['hero_button1_level'] }}</a>
                   <a class="site-btn primary-btn" href="{{ $data['hero_button2_url'] }}" target="{{ $data['hero_button2_target'] }}">{{ $data['hero_button2_lavel'] }}</a>
                </div>
             </div>
          </div>
          {{-- Static Content --}}
          <div class="col-xxl-6 col-xl-6 col-lg-6">
            <div class="banner-illustration">
               <div class="thumb">
                  <img src="{{ asset($data['hero_right_img']) }}" alt="illustration">
               </div>
               <div class="shape-text">
                  <span>
                     <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2065_16)">
                        <path d="M24 48C37.2548 48 48 37.2548 48 24C48 10.7452 37.2548 0 24 0C10.7452 0 0 10.7452 0 24C0 37.2548 10.7452 48 24 48Z" fill="#315765"></path>
                        <path d="M32 25.0004C32 30.0004 28.5 32.5005 24.34 33.9505C24.1222 34.0243 23.8855 34.0207 23.67 33.9405C19.5 32.5005 16 30.0004 16 25.0004V18.0004C16 17.7352 16.1054 17.4809 16.2929 17.2933C16.4804 17.1058 16.7348 17.0004 17 17.0004C19 17.0004 21.5 15.8004 23.24 14.2804C23.4519 14.0994 23.7214 14 24 14C24.2786 14 24.5481 14.0994 24.76 14.2804C26.51 15.8104 29 17.0004 31 17.0004C31.2652 17.0004 31.5196 17.1058 31.7071 17.2933C31.8946 17.4809 32 17.7352 32 18.0004V25.0004Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M21 24L23 26L27 22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                        <defs>
                        <clipPath id="clip0_2065_16">
                        <rect width="48" height="48" fill="white"></rect>
                        </clipPath>
                        </defs>
                     </svg>
                  </span>
                  <h6>
                    {{ $data['hero_right_animate_title'] }}
                  </h6>
              </div>
              <div class="user-one">
                  <img src="{{ asset($data['hero_right_top_img']) }}" alt="user-01">
              </div>
              <div class="user-two">
                  <img src="{{ asset($data['hero_left_shape_img']) }}" alt="user-01">
              </div>
            </div>
         </div>
       </div>
    </div>
 </section>
