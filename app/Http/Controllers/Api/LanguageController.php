<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function changeLanguage($locale)
    {
        session()->put('locale', $locale);

        return response()->json([
            'status' => true,
            'locale' => $locale,
            'translations_keys' => $this->getTranslationKeys($locale),
            'message' => __('Language changed successfully'),
        ]);
    }

    private function getTranslationKeys($locale)
    {
        $filePath = resource_path("lang/app/$locale.json");
        if (! file_exists($filePath)) {
            return [];
        }

        $translations = json_decode(file_get_contents($filePath), true);

        return $translations;
    }
}
