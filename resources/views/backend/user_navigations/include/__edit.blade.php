<div class="modal fade" id="editNav" tabindex="-1" aria-labelledby="editNavModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="popup-body-text">
                    <form action="{{ route('admin.user.navigation.update') }}" method="POST" id="edit-form">
                        @csrf
                        <input type="hidden" name="type">
                        <h3 class="title mb-4">{{ __('Update Navigation') }}</h3>
                        <div class="site-tab-bars mb-3">
                            <ul class="nav nav-pills" id="pills-tab-nav" role="tablist">
                                @foreach ($languages as $language)
                                    <li class="nav-item" role="presentation">
                                        <a href="#" class="nav-link {{ $loop->index == 0 ? 'active' : '' }}"
                                            id="pills-nav-tab-{{ $language->locale }}" data-bs-toggle="pill"
                                            data-bs-target="#{{ $language->locale }}-nav" type="button" role="tab"
                                            aria-controls="pills-nav"
                                            aria-selected="{{ $loop->index == 0 ? 'true' : 'false' }}">
                                            <i data-lucide="languages"></i>{{ $language->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tab-content" id="pills-tabContent-nav">
                            @foreach ($languages as $language)
                                <div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}"
                                    id="{{ $language->locale }}-nav" role="tabpanel"
                                    aria-labelledby="pills-nav-tab-{{ $language->locale }}">
                                    <div class="site-input-groups">
                                        <label for="name_{{ $language->locale }}"
                                            class="box-input-label">{{ __('Menu Name (') . $language->name . __('):') }}</label>
                                        <input type="text" name="translation[{{ $language->locale }}]"
                                            class="box-input mb-0 name" id="name_{{ $language->locale }}"
                                            value="{{ old('translation.' . $language->locale) }}"
                                            placeholder="Menu Name" required />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="action-btns mt-3">
                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                <i data-lucide="check"></i>
                                {{ __('Update') }}
                            </button>
                            <a href="#" class="site-btn-sm red-btn" data-bs-dismiss="modal">
                                <i data-lucide="x"></i>
                                {{ __('Close') }}
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
