<form action="{{ request()->url() }}" method="get" id="filterForm">
    <div class="table-filter">
        <div class="filter">
            <div class="search">
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Search..." />
            </div>
            <button type="submit" class="apply-btn"><i data-lucide="search"></i>{{ __('Search') }}</button>
        </div>
        @if (isset($filter))
            <div class="filter d-flex">
                {{-- @include('backend.language.include.select', ['name' => 'language', 'items' => $languages, 'submit' => true, 'selected' => $language]) --}}
                @include('backend.language.include.select', [
                    'name' => 'group',
                    'items' => $groups,
                    'submit' => true,
                    'selected' => Request::get('group'),
                    'optional' => true,
                ])
            </div>
        @endif
    </div>
</form>
@push('single-script')
    <script>
        (function($) {
            "use strict";
            $('#perPage').on('change', function() {
                $('#filterForm').submit();
            });

            $('#order').on('change', function() {
                $('#filterForm').submit();
            });

            $('#status').on('change', function() {
                $('#filterForm').submit();
            });
        })(jQuery);
    </script>
@endpush
