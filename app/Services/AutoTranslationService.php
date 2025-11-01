<?php

namespace App\Services;

use Exception;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AutoTranslationService
{
    protected TranslateClient $translator;

    protected string $sourceLocale;

    protected array $preservePatterns;

    protected int $batchSize;

    protected int $rateLimitDelay;

    protected array $retryConfig;

    protected bool $logEnabled;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sourceLocale = config('auto-translation.source_locale', 'en');
        $this->preservePatterns = config('auto-translation.preserve_patterns', []);
        $this->batchSize = config('auto-translation.batch_size', 100);
        $this->rateLimitDelay = config('auto-translation.rate_limit_delay', 100);
        $this->retryConfig = config('auto-translation.retry', ['attempts' => 3, 'delay' => 1000]);
        $this->logEnabled = config('auto-translation.log_enabled', true);

        $this->initializeTranslator();
    }

    /**
     * Initialize Google Translate Client
     */
    protected function initializeTranslator(): void
    {
        try {
            $apiKey = config('auto-translation.google.api_key');

            if (empty($apiKey)) {
                throw new Exception('Google Translate API key is not configured. Please set GOOGLE_TRANSLATE_API_KEY in your .env file.');
            }

            $this->translator = new TranslateClient([
                'key' => $apiKey,
            ]);
        } catch (Exception $e) {
            $this->log('error', 'Failed to initialize Google Translate Client: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Translate all language files for a specific locale
     *
     * @param  string  $targetLocale  Target language code (e.g., 'fr', 'es', 'ar')
     * @param  bool  $force  Overwrite existing translations
     * @return array Statistics about the translation
     */
    public function translateLanguage(string $targetLocale, bool $force = false): array
    {
        $stats = [
            'total' => 0,
            'translated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'files' => [],
        ];

        $this->log('info', "Starting translation to {$targetLocale}");

        try {
            // Translate Laravel language files
            $stats['files']['laravel'] = $this->translateLaravelFiles($targetLocale, $force);

            // Translate app-specific JSON file
            $stats['files']['app'] = $this->translateAppJson($targetLocale, $force);

            // Calculate totals
            foreach ($stats['files'] as $fileStats) {
                $stats['total'] += $fileStats['total'];
                $stats['translated'] += $fileStats['translated'];
                $stats['skipped'] += $fileStats['skipped'];
                $stats['errors'] += $fileStats['errors'];
            }

            $this->log('info', "Translation completed for {$targetLocale}. Total: {$stats['total']}, Translated: {$stats['translated']}, Skipped: {$stats['skipped']}, Errors: {$stats['errors']}");
        } catch (Exception $e) {
            $this->log('error', "Translation failed for {$targetLocale}: ".$e->getMessage());
            $stats['errors']++;
        }

        return $stats;
    }

    /**
     * Translate Laravel language files (PHP files in resources/lang/{locale}/)
     */
    protected function translateLaravelFiles(string $targetLocale, bool $force = false): array
    {
        $stats = ['total' => 0, 'translated' => 0, 'skipped' => 0, 'errors' => 0];

        $sourcePath = resource_path("lang/{$this->sourceLocale}");
        $targetPath = resource_path("lang/{$targetLocale}");

        if (! File::exists($sourcePath)) {
            $this->log('warning', "Source language path does not exist: {$sourcePath}");

            return $stats;
        }

        // Create target directory if it doesn't exist
        if (! File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
        }

        // Get all PHP files from source directory
        $files = File::files($sourcePath);

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $targetFile = $targetPath.DIRECTORY_SEPARATOR.$filename;

            try {
                // Load source translations
                $sourceTranslations = include $file->getPathname();

                // Load existing translations or create empty array
                $targetTranslations = File::exists($targetFile) ? include $targetFile : [];

                // Translate
                $result = $this->translateArray($sourceTranslations, $targetTranslations, $targetLocale, $force);

                // Save translated file
                $this->savePhpFile($targetFile, $result['translations']);

                $stats['total'] += $result['total'];
                $stats['translated'] += $result['translated'];
                $stats['skipped'] += $result['skipped'];

                $this->log('info', "Translated file: {$filename} ({$result['translated']}/{$result['total']})");
            } catch (Exception $e) {
                $stats['errors']++;
                $this->log('error', "Failed to translate file {$filename}: ".$e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Translate app-specific JSON file (resources/lang/app/{locale}.json)
     */
    protected function translateAppJson(string $targetLocale, bool $force = false): array
    {
        $stats = ['total' => 0, 'translated' => 0, 'skipped' => 0, 'errors' => 0];

        $sourceFile = resource_path("lang/app/{$this->sourceLocale}.json");
        $targetFile = resource_path("lang/app/{$targetLocale}.json");

        if (! File::exists($sourceFile)) {
            $this->log('warning', "Source app JSON file does not exist: {$sourceFile}");

            return $stats;
        }

        try {
            // Load source translations
            $sourceTranslations = json_decode(File::get($sourceFile), true);

            // Load existing translations or create empty array
            $targetTranslations = File::exists($targetFile)
                ? json_decode(File::get($targetFile), true)
                : [];

            // Translate
            $result = $this->translateArray($sourceTranslations, $targetTranslations, $targetLocale, $force);

            // Save translated file
            File::put($targetFile, json_encode($result['translations'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $stats['total'] = $result['total'];
            $stats['translated'] = $result['translated'];
            $stats['skipped'] = $result['skipped'];

            $this->log('info', "Translated app JSON: {$targetLocale}.json ({$result['translated']}/{$result['total']})");
        } catch (Exception $e) {
            $stats['errors']++;
            $this->log('error', "Failed to translate app JSON: ".$e->getMessage());
        }

        return $stats;
    }

    /**
     * Recursively translate an array of translations
     */
    protected function translateArray(array $sourceArray, array $targetArray, string $targetLocale, bool $force = false): array
    {
        $stats = ['total' => 0, 'translated' => 0, 'skipped' => 0, 'translations' => []];

        // Flatten the arrays for easier processing
        $sourceDotted = Arr::dot($sourceArray);
        $targetDotted = Arr::dot($targetArray);

        // Prepare batch for translation
        $batch = [];
        $batchKeys = [];

        foreach ($sourceDotted as $key => $value) {
            $stats['total']++;

            // Skip if not a string
            if (! is_string($value)) {
                $stats['translations'][$key] = $value;
                $stats['skipped']++;
                continue;
            }

            // Skip if key is in skip list
            if ($this->shouldSkipKey($key)) {
                $stats['translations'][$key] = $value;
                $stats['skipped']++;
                continue;
            }

            // Skip if already translated and not forcing
            if (! $force && isset($targetDotted[$key]) && ! empty($targetDotted[$key])) {
                $stats['translations'][$key] = $targetDotted[$key];
                $stats['skipped']++;
                continue;
            }

            // Add to batch
            $batch[] = $value;
            $batchKeys[] = $key;

            // Process batch when it reaches batch size
            if (count($batch) >= $this->batchSize) {
                $this->processBatch($batch, $batchKeys, $targetLocale, $stats);
                $batch = [];
                $batchKeys = [];
            }
        }

        // Process remaining items in batch
        if (count($batch) > 0) {
            $this->processBatch($batch, $batchKeys, $targetLocale, $stats);
        }

        // Undot the translations
        $stats['translations'] = Arr::undot($stats['translations']);

        return $stats;
    }

    /**
     * Process a batch of translations
     */
    protected function processBatch(array $batch, array $batchKeys, string $targetLocale, array &$stats): void
    {
        try {
            // Preserve placeholders before translation
            $placeholders = [];
            $processedBatch = [];

            foreach ($batch as $index => $text) {
                $result = $this->preservePlaceholders($text);
                $processedBatch[] = $result['text'];
                $placeholders[$index] = $result['placeholders'];
            }

            // Translate batch
            $translations = $this->translateBatch($processedBatch, $targetLocale);

            // Restore placeholders and save translations
            foreach ($translations as $index => $translatedText) {
                $key = $batchKeys[$index];
                $restoredText = $this->restorePlaceholders($translatedText, $placeholders[$index]);
                $stats['translations'][$key] = $restoredText;
                $stats['translated']++;
            }

            // Rate limiting
            usleep($this->rateLimitDelay * 1000);
        } catch (Exception $e) {
            $this->log('error', 'Batch translation failed: '.$e->getMessage());

            // Fall back to source text for failed translations
            foreach ($batchKeys as $index => $key) {
                $stats['translations'][$key] = $batch[$index];
                $stats['errors']++;
            }
        }
    }

    /**
     * Translate a batch of texts using Google Translate API
     */
    protected function translateBatch(array $texts, string $targetLocale): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->retryConfig['attempts']) {
            try {
                $result = $this->translator->translateBatch($texts, [
                    'target' => $targetLocale,
                    'source' => $this->sourceLocale,
                ]);

                return array_map(function ($translation) {
                    return $translation['text'];
                }, $result);
            } catch (Exception $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt < $this->retryConfig['attempts']) {
                    usleep($this->retryConfig['delay'] * 1000);
                }
            }
        }

        throw $lastException;
    }

    /**
     * Preserve placeholders in text before translation
     */
    protected function preservePlaceholders(string $text): array
    {
        $placeholders = [];
        $counter = 0;

        foreach ($this->preservePatterns as $pattern) {
            $text = preg_replace_callback($pattern, function ($matches) use (&$placeholders, &$counter) {
                $placeholder = "___PLACEHOLDER_{$counter}___";
                $placeholders[$placeholder] = $matches[0];
                $counter++;

                return $placeholder;
            }, $text);
        }

        return [
            'text' => $text,
            'placeholders' => $placeholders,
        ];
    }

    /**
     * Restore placeholders in translated text
     */
    protected function restorePlaceholders(string $text, array $placeholders): string
    {
        foreach ($placeholders as $placeholder => $original) {
            $text = str_replace($placeholder, $original, $text);
        }

        return $text;
    }

    /**
     * Check if a translation key should be skipped
     */
    protected function shouldSkipKey(string $key): bool
    {
        $skipKeys = config('auto-translation.skip_keys', []);

        foreach ($skipKeys as $skipKey) {
            if (Str::startsWith($key, $skipKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save PHP array to file
     */
    protected function savePhpFile(string $filePath, array $data): void
    {
        $content = "<?php\n\nreturn ".var_export($data, true).";\n";
        File::put($filePath, $content);
    }

    /**
     * Log translation activity
     */
    protected function log(string $level, string $message): void
    {
        if ($this->logEnabled) {
            Log::$level("[AutoTranslation] {$message}");
        }
    }

    /**
     * Check if Google Translate API is configured
     */
    public function isConfigured(): bool
    {
        return ! empty(config('auto-translation.google.api_key'));
    }

    /**
     * Get supported languages from Google Translate
     */
    public function getSupportedLanguages(): array
    {
        try {
            $languages = $this->translator->languages();

            return array_map(function ($lang) {
                return [
                    'code' => $lang['code'],
                    'name' => $lang['name'] ?? $lang['code'],
                ];
            }, $languages);
        } catch (Exception $e) {
            $this->log('error', 'Failed to get supported languages: '.$e->getMessage());

            return [];
        }
    }
}
