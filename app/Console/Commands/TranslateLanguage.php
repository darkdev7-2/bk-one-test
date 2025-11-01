<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Services\AutoTranslationService;
use Exception;
use Illuminate\Console\Command;

class TranslateLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:language
                            {locale : The target language code (e.g., fr, es, ar)}
                            {--force : Overwrite existing translations}
                            {--all : Translate all active languages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically translate language files using Google Translate API';

    /**
     * Execute the console command.
     */
    public function handle(AutoTranslationService $translationService): int
    {
        // Check if service is configured
        if (! $translationService->isConfigured()) {
            $this->error('Google Translate API is not configured!');
            $this->info('Please set GOOGLE_TRANSLATE_API_KEY in your .env file.');
            $this->newLine();
            $this->info('To get an API key:');
            $this->info('1. Go to https://console.cloud.google.com/');
            $this->info('2. Create a new project or select an existing one');
            $this->info('3. Enable the Cloud Translation API');
            $this->info('4. Create credentials (API Key)');
            $this->info('5. Add the API key to your .env file as GOOGLE_TRANSLATE_API_KEY');

            return self::FAILURE;
        }

        $force = $this->option('force');

        // Translate all languages
        if ($this->option('all')) {
            return $this->translateAllLanguages($translationService, $force);
        }

        // Translate single language
        $locale = $this->argument('locale');

        return $this->translateSingleLanguage($translationService, $locale, $force);
    }

    /**
     * Translate all active languages
     */
    protected function translateAllLanguages(AutoTranslationService $translationService, bool $force): int
    {
        $languages = Language::where('status', 1)
            ->where('locale', '!=', config('auto-translation.source_locale'))
            ->get();

        if ($languages->isEmpty()) {
            $this->warn('No active languages found to translate.');

            return self::SUCCESS;
        }

        $this->info("Translating {$languages->count()} language(s)...");
        $this->newLine();

        $overallStats = [
            'total' => 0,
            'translated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($languages as $language) {
            $this->info("Translating: {$language->name} ({$language->locale})");

            $stats = $this->translateSingleLanguage($translationService, $language->locale, $force, false);

            if (is_array($stats)) {
                $overallStats['total'] += $stats['total'];
                $overallStats['translated'] += $stats['translated'];
                $overallStats['skipped'] += $stats['skipped'];
                $overallStats['errors'] += $stats['errors'];
            }

            $this->newLine();
        }

        // Display overall summary
        $this->displaySummary($overallStats);

        return self::SUCCESS;
    }

    /**
     * Translate a single language
     */
    protected function translateSingleLanguage(
        AutoTranslationService $translationService,
        string $locale,
        bool $force,
        bool $displaySummary = true
    ): int|array {
        // Validate language exists
        $language = Language::where('locale', $locale)->first();

        if (! $language) {
            $this->error("Language '{$locale}' not found in the database.");
            $this->info('Please create the language first from the admin panel.');

            return self::FAILURE;
        }

        // Check if translating from source to source
        if ($locale === config('auto-translation.source_locale')) {
            $this->warn("Cannot translate source language to itself.");

            return self::FAILURE;
        }

        try {
            $this->info("Starting translation for: {$language->name} ({$locale})");

            if ($force) {
                $this->warn('Force mode enabled - existing translations will be overwritten.');
            }

            $this->newLine();

            // Start progress bar (we don't know the exact count, so we'll use an indeterminate progress)
            $this->output->write('Translating');

            // Perform translation
            $stats = $translationService->translateLanguage($locale, $force);

            $this->newLine(2);
            $this->info('Translation completed!');
            $this->newLine();

            if ($displaySummary) {
                $this->displaySummary($stats);
            }

            return $displaySummary ? self::SUCCESS : $stats;
        } catch (Exception $e) {
            $this->newLine();
            $this->error('Translation failed: '.$e->getMessage());
            $this->newLine();
            $this->error('Stack trace:');
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    /**
     * Display translation summary
     */
    protected function displaySummary(array $stats): void
    {
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Items', $stats['total']],
                ['Translated', "<fg=green>{$stats['translated']}</>"],
                ['Skipped', "<fg=yellow>{$stats['skipped']}</>"],
                ['Errors', $stats['errors'] > 0 ? "<fg=red>{$stats['errors']}</>" : $stats['errors']],
            ]
        );

        if (isset($stats['files'])) {
            $this->newLine();
            $this->info('File Statistics:');

            foreach ($stats['files'] as $fileType => $fileStats) {
                $this->line("  {$fileType}: {$fileStats['translated']}/{$fileStats['total']} translated");
            }
        }
    }
}
