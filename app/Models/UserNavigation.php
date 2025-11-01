<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNavigation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts()
    {
        return [
            'translation' => 'json',
        ];
    }

    /**
     * Get the translated name for the navigation item.
     *
     * @param  string|null  $locale
     * @return string
     */
    public function getTranslatedName($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $this->translation;
        if (is_string($translations)) {
            $translations = json_decode($translations, true);
        }
        if (is_array($translations) && isset($translations[$locale]) && $translations[$locale]) {
            return $translations[$locale];
        }

        return $this->name;
    }
}
