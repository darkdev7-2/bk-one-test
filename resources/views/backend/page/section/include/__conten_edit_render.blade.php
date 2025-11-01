<h3 class="title">{{ __('Edit Content') }}</h3>

<div class="site-tab-bars">
    <ul class="nav nav-pills" id="pills-tab-render" role="tablist">
        @foreach($languages as $language)
        <li class="nav-item" role="presentation">
            <a href="" class="nav-link  {{ $loop->index == 0 ?'active' : '' }}" id="pills-render-tab"
                data-bs-toggle="pill" data-bs-target="#{{$language->locale}}-render" type="button" role="tab"
                aria-controls="pills-render" aria-selected="true"><i data-lucide="languages"></i>{{$language->name}}</a>
        </li>
        @endforeach
    </ul>
</div>

<div class="tab-content" id="pills-tabContent">
    @foreach($groupData as $key => $landingContent)
    <div class="tab-pane fade {{ $loop->index == 0 ?'show active' : '' }}" id="{{$key}}-render" role="tabpanel"
        aria-labelledby="pills-render-tab">

        <div class="row">
            <div class="col-xl-12">
                <form action="{{ route('admin.page.content-update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $landingContent['id'] }}">
                    <input type="hidden" name="locale_id" value="{{ $landingContent['locale_id'] }}">
                    <input type="hidden" name="locale" value="{{ $key }}">
                    @if($key == 'en')
                    <div class="site-input-groups">
                        @if($landingContent['type'] == 'solutions' || $landingContent['type'] == 'card')
                        <label for="" class="box-input-label">{{ __('Icon Name') }} <a class="link"
                                href="https://fontawesome.com/icons"
                                target="_blank">{{ __('Fontawesome') }}</a>:</label>
                        <input type="text" name="icon" class="box-input mb-0 icon" value="{{ $landingContent['icon'] }}"
                            placeholder="Icon Class" required="" />
                        @elseif($landingContent['type'] == 'bankingsolution' || $landingContent['type'] == 'whychooseus' || $landingContent['type'] ==
                        'experiencesection' || $landingContent['type'] == 'experiencesection')
                         <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('Icon Type:') }}</label>
                            <select class="form-select" name="type" id="editIconTypes">
                                <option value="class" {{ !content_exists($landingContent['icon']) ? 'selected' : '' }}>{{ __('Icon Class') }}</option>
                                <option value="image" {{ content_exists($landingContent['icon']) ? 'selected' : '' }}>{{ __('Image') }}</option>
                            </select>
                        </div>
                        <div class="site-input-groups" id="editClassField">
                            <label for="" class="box-input-label">{{ __('Icon Class') }} <a class="link"
                                                                                            href="https://lucide.dev/icons/"
                                                                                            target="_blank">{{ __('Lucide') }}</a>:</label>
                            <input type="text" name="class_name" @if(!content_exists($landingContent['icon'])) value="{{ $landingContent['icon'] }}" @endif class="box-input mb-0" placeholder="Icon Class"/>
                        </div>

                        <div class="site-input-groups" id="editImageField">
                            <label class="box-input-label" for="editContentIcon">{{ __('Icon:') }}</label>
                            <div class="wrap-custom-file">
                                <input type="file" name="icon" id="editContentIcon" accept=".gif, .jpg, .png"/>
                                <label for="editContentIcon" class="file-ok"
                                style="background-image: url({{ asset($landingContent['icon']) }})">
                                    <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}" alt=""/>
                                    <span>{{ __('Upload Icon') }}</span>
                                </label>
                            </div>
                        </div>

                        @elseif($landingContent['type'] == 'workstepsection' || $landingContent['type'] ==
                        'powerfulsection' || $landingContent['type'] == 'howitworks')
                        <label class="box-input-label">{{ __('Icon') }}</label>
                        <div class="wrap-custom-file">
                            <input type="file" name="icon" id="uploadIcon" accept=".gif, .jpg, .png" />
                            <label for="uploadIcon" id="iconPreview" class="file-ok"
                                style="background-image: url( {{asset($landingContent['icon']) }} )">
                                <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                <span>{{ __('Upload') }} </span>
                            </label>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="site-input-groups">
                        <label for="" class="box-input-label">{{ __('Title:') }}</label>
                        <input type="text" name="title" value="{{ $landingContent['title'] }}"
                            class="box-input mb-0 title0" required="" />
                    </div>

                    @if($landingContent['type'] != 'experiencesection' && $landingContent['type'] != 'workstepsection')
                    <div class="site-input-groups mb-0">
                        <label for="" class="box-input-label">{{ __('Description:') }}</label>
                        <textarea name="description" class="form-textarea description"
                            placeholder="Description">{{ $landingContent['description'] }}</textarea>
                    </div>
                    @endif

                    @if($key == 'en' && $landingContent['type'] == 'bankingsolution' && $landingContent['type'] == 'whychooseus')
                    <div class="site-input-groups">
                        <label class="box-input-label">{{ __('Image') }}</label>
                        <div class="wrap-custom-file">
                            <input type="file" name="photo" id="uploadPhoto" accept=".gif, .jpg, .png" />
                            <label for="uploadPhoto" class="file-ok"
                                style="background-image: url( {{asset($landingContent['photo']) }} )">
                                <img class="upload-icon" src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                <span>{{ __('Upload') }}</span>
                            </label>
                        </div>
                    </div>
                    @endif

                    <div class="action-btns">
                        <button type="submit" class="site-btn-sm primary-btn me-2">
                            <i data-lucide="check"></i>
                            {{ __(' Save Changes') }}
                        </button>
                        <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i data-lucide="x"></i>
                            {{ __(' Close') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    $('#uploadPhoto').on('change', function () {
        filePreview($(this), 'label[for=uploadPhoto]');
    });

    $('#uploadIcon').on('change', function () {
        filePreview($(this), 'label[for=uploadIcon]');
    })

    function filePreview(el, target) {
        // Refs
        var file = $(el),
            label = $(target),
            labelText = label.find('span');

        var fileName = file.val().split('\\').pop();
        var tmppath = URL.createObjectURL(file.get(0).files[0]);

        label.css('background-image', 'url(' + tmppath + ')');
        labelText.text(fileName);
    }

    $('#editIconTypes').on('change',function(){
        initIconType();
    });
</script>
