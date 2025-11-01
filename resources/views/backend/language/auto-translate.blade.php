@extends('backend.layouts.app')
@section('title')
    {{ __('Auto Translation') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Auto Translation') }}</h2>
                            <div>
                                <a href="{{ route('admin.language.index') }}" class="title-btn"><i data-lucide="arrow-left"></i>{{ __('Back to Languages') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">

                @if(!$isConfigured)
                    <!-- Configuration Warning -->
                    <div class="col-xl-12">
                        <div class="alert alert-warning">
                            <h4><i data-lucide="alert-triangle"></i> {{ __('Google Translate API Not Configured') }}</h4>
                            <p>{{ __('To use automatic translation, you need to configure the Google Translate API key.') }}</p>
                            <hr>
                            <h5>{{ __('Setup Instructions:') }}</h5>
                            <ol>
                                <li>{{ __('Go to') }} <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                <li>{{ __('Create a new project or select an existing one') }}</li>
                                <li>{{ __('Enable the Cloud Translation API') }}</li>
                                <li>{{ __('Create credentials (API Key)') }}</li>
                                <li>{{ __('Add the API key to your .env file:') }}
                                    <pre class="mt-2">GOOGLE_TRANSLATE_API_KEY=your_api_key_here</pre>
                                </li>
                                <li>{{ __('Clear your config cache:') }}
                                    <pre class="mt-2">php artisan config:clear</pre>
                                </li>
                            </ol>
                            <p class="mb-0"><strong>{{ __('Documentation:') }}</strong> <a href="https://cloud.google.com/translate/docs/setup" target="_blank">https://cloud.google.com/translate/docs/setup</a></p>
                        </div>
                    </div>
                @else
                    <!-- Configuration Success -->
                    <div class="col-xl-12">
                        <div class="alert alert-success">
                            <i data-lucide="check-circle"></i> {{ __('Google Translate API is configured and ready to use!') }}
                        </div>
                    </div>

                    <!-- Translate All Button -->
                    <div class="col-xl-12 mb-3">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h4 class="title">{{ __('Bulk Translation') }}</h4>
                            </div>
                            <div class="site-card-body">
                                <p>{{ __('Translate all active languages at once. This may take several minutes depending on the number of languages and text to translate.') }}</p>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.language.translate-all') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('{{ __('Are you sure you want to translate all active languages? This will skip already translated items.') }}')">
                                            <i data-lucide="globe"></i> {{ __('Translate All Languages') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.language.translate-all') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="force" value="1">
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('{{ __('Are you sure? This will overwrite ALL existing translations!') }}')">
                                            <i data-lucide="refresh-cw"></i> {{ __('Force Retranslate All') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Languages -->
                    <div class="col-xl-12">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h4 class="title">{{ __('Translate Individual Languages') }}</h4>
                            </div>
                            <div class="site-card-body">
                                <div class="site-table table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Language Name') }}</th>
                                                <th>{{ __('Code') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($languages as $language)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $language->name }}</strong>
                                                        @if($language->is_default)
                                                            <span class="badge bg-primary ms-2">{{ __('Default') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <code>{{ $language->locale }}</code>
                                                    </td>
                                                    <td>
                                                        @if($language->status)
                                                            <div class="site-badge success">{{ __('Active') }}</div>
                                                        @else
                                                            <div class="site-badge pending">{{ __('Inactive') }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <form action="{{ route('admin.language.translate', $language) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-primary" title="{{ __('Translate missing items only') }}">
                                                                    <i data-lucide="languages"></i> {{ __('Translate') }}
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.language.translate', $language) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="force" value="1">
                                                                <button type="submit" class="btn btn-sm btn-warning" title="{{ __('Retranslate everything') }}" onclick="return confirm('{{ __('This will overwrite all existing translations for this language. Are you sure?') }}')">
                                                                    <i data-lucide="refresh-cw"></i> {{ __('Force Retranslate') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ __('No languages available for translation.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="col-xl-12 mt-3">
                        <div class="site-card">
                            <div class="site-card-header">
                                <h4 class="title">{{ __('How It Works') }}</h4>
                            </div>
                            <div class="site-card-body">
                                <h5>{{ __('Translation Modes:') }}</h5>
                                <ul>
                                    <li><strong>{{ __('Translate:') }}</strong> {{ __('Only translates missing/empty items. Existing translations are preserved.') }}</li>
                                    <li><strong>{{ __('Force Retranslate:') }}</strong> {{ __('Overwrites all translations, including existing ones.') }}</li>
                                </ul>

                                <h5 class="mt-3">{{ __('Command Line Usage:') }}</h5>
                                <p>{{ __('You can also use the command line to translate languages:') }}</p>
                                <pre>
# Translate a specific language
php artisan translate:language fr

# Force retranslate
php artisan translate:language fr --force

# Translate all active languages
php artisan translate:language en --all
                                </pre>

                                <h5 class="mt-3">{{ __('What Gets Translated:') }}</h5>
                                <ul>
                                    <li>{{ __('Laravel language files (resources/lang/{locale}/*.php)') }}</li>
                                    <li>{{ __('App-specific translations (resources/lang/app/{locale}.json)') }}</li>
                                    <li>{{ __('Placeholders like :attribute and {variable} are preserved') }}</li>
                                </ul>

                                <h5 class="mt-3">{{ __('Pricing Information:') }}</h5>
                                <p>{{ __('Google Translate API charges approximately $20 per 1 million characters. Most applications will stay well within the free tier or have minimal costs.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Add loading indicator when translation starts
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('Translating...') }}';
                }
            });
        });
    </script>
@endsection
