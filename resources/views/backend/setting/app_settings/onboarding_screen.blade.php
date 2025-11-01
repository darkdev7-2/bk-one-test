@extends('backend.setting.index')
@section('setting-title')
    {{ __('App Settings') }}
@endsection
@section('setting-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="site-card">
                    <div class="site-card-header">
                        <h3 class="title">{{ __('Onboarding Images') }}</h3>
                    </div>
                    <div class="site-card-body">
                        <form action="{{ route('admin.page.setting.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="site-input-groups row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                    {{ __('One Image') }}
                                </div>
                                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                    <div class="wrap-custom-file">
                                        <input type="file" name="app_splash_one_image" id="app_splash_one_image"
                                            accept=".gif, .jpg, .png,.jpeg" />
                                        <label for="app_splash_one_image" class="file-ok"
                                            style="background-image: url({{ asset(getPageSetting('app_splash_one_image')) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Update Image') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="site-input-groups row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                    {{ __('Two Image') }}
                                </div>
                                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                    <div class="wrap-custom-file">
                                        <input type="file" name="app_splash_two_image" id="app_splash_two_image"
                                            accept=".gif, .jpg, .png,.jpeg" />
                                        <label for="app_splash_two_image" class="file-ok"
                                            style="background-image: url({{ asset(getPageSetting('app_splash_two_image')) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Update Image') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="site-input-groups row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                    {{ __('Three Image') }}
                                </div>
                                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                    <div class="wrap-custom-file">
                                        <input type="file" name="app_splash_three_image" id="app_splash_three_image"
                                            accept=".gif, .jpg, .png,.jpeg" />
                                        <label for="app_splash_three_image" class="file-ok"
                                            style="background-image: url({{ asset(getPageSetting('app_splash_three_image')) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Update Image') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="site-input-groups row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-label">
                                    {{ __('Four Image') }}
                                </div>
                                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                                    <div class="wrap-custom-file">
                                        <input type="file" name="app_splash_four_image" id="app_splash_four_image"
                                            accept=".gif, .jpg, .png,.jpeg" />
                                        <label for="app_splash_four_image" class="file-ok"
                                            style="background-image: url({{ asset(getPageSetting('app_splash_four_image')) }})">
                                            <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}"
                                                alt="" />
                                            <span>{{ __('Update Image') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-sm-3 col-sm-9">
                                    <button type="submit"
                                        class="site-btn-sm primary-btn">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
