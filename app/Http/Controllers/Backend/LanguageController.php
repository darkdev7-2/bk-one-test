<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use App\Models\LandingPage;
use App\Models\Language;
use App\Models\Page;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JoeDixon\Translation\Drivers\Translation;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    private Translation $translation;

    private string $languageFilesPath;

    public function __construct(Translation $translation)
    {
        $this->middleware('permission:language-setting');
        $this->translation = $translation;
        $this->languageFilesPath = resource_path('lang');
    }

    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 15;
        $order = $request->order ?? 'asc';
        $search = $request->search ?? null;
        $languages = Language::order($order)
            ->search($search)
            ->paginate($perPage);

        return view('backend.language.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('backend.language.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => ['required', 'unique:languages,locale'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $data = [
            'name' => $input['name'],
            'locale' => $input['code'],
            'is_default' => $input['is_default'],
            'is_rtl' => $input['is_rtl'],
            'status' => $input['status'],
        ];

        if ($input['is_default']) {
            DB::table('languages')->update(['is_default' => 0]);
            $data['status'] = 1;
        }

        $this->translation->addLanguage($input['code'], $input['name']);

        Language::create($data);

        // Copy app keywords from en to new language
        $filePath = resource_path('lang/app/en.json');
        $translations = json_decode(file_get_contents($filePath), true);
        file_put_contents(resource_path("lang/app/$input[code].json"), json_encode($translations, JSON_PRETTY_PRINT));

        $contents = LandingContent::where('locale', 'en')->get();

        foreach ($contents as $content) {
            $new = $content->replicate();
            $new->locale = $input['code'];
            $new->save();
        }

        $LandingPages = LandingPage::where('locale', 'en')->get();

        foreach ($LandingPages as $page) {
            $new = $page->replicate();
            $new->locale = $input['code'];
            $new->save();
        }

        $pages = Page::where('locale', 'en')->get();

        foreach ($pages as $page) {
            $new = $page->replicate();
            $new->locale = $input['code'];
            $new->save();
        }

        notify()->success(__('Language added successfully!'));

        return redirect()->route('admin.language.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(language $language)
    {
        return view('backend.language.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, language $language)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => ['required', 'unique:languages,locale,'.$language->id],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $data = [
            'name' => $input['name'],
            'locale' => $input['code'],
            'is_rtl' => $input['is_rtl'],
            'is_default' => (bool) $input['is_default'],
            'status' => (bool) $input['status'],
        ];

        if ($language->is_default && ! $input['is_default']) {
            notify()->error('Please set default language', 'Error');

            return redirect()->back();
        }

        if ($input['is_default']) {
            DB::table('languages')->update(['is_default' => 0]);
            $data['status'] = 1;
        }

        /* Existing File name */
        $filePath = "{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$language->locale}.json";
        /* New File name */
        $newFileName = "{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$input['code']}.json";
        rename($filePath, $newFileName);

        /* Existing File name */
        $folderPath = "{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$language->locale}";
        /* New File name */
        $newFolderName = "{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$input['code']}";
        rename($folderPath, $newFolderName);

        // Update app keywords
        $filePath = resource_path("lang/app/{$language->locale}.json");
        $newFilePath = resource_path("lang/app/{$input['code']}.json");
        rename($filePath, $newFilePath);

        // Update landing contents & pages
        $contents = LandingContent::where('locale', $language->locale)->get();

        foreach ($contents as $content) {
            $new = $content;
            $new->locale = $input['code'];
            $new->save();
        }

        $landingPages = LandingPage::where('locale', $language->locale)->get();

        foreach ($landingPages as $page) {
            $new = $page;
            $new->locale = $input['code'];
            $new->save();
        }

        $pages = Page::where('locale', $language->locale)->get();

        foreach ($pages as $page) {
            $new = $page;
            $new->locale = $input['code'];
            $new->save();
        }

        $language->update($data);

        notify()->success(__('Language updated successfully!'));

        return redirect()->route('admin.language.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(language $language)
    {
        // Delete json & directory
        File::delete("{$this->languageFilesPath}".DIRECTORY_SEPARATOR."{$language->locale}.json");
        File::deleteDirectory("{$this->languageFilesPath}".DIRECTORY_SEPARATOR."$language->locale");

        // Delete landing contents & pages
        LandingPage::where('locale', $language->locale)->delete();
        LandingContent::where('locale', $language->locale)->delete();
        Page::where('locale', $language->locale)->delete();

        // Delete app keywords
        File::delete(resource_path("lang/app/{$language->locale}.json"));

        $language->delete();

        notify()->success(__('Language deleted successfully!'));

        return redirect()->route('admin.language.index');
    }

    public function languageKeyword(Request $request, $language)
    {
        if ($request->has('language') && $request->get('language') !== $language) {
            return redirect()
                ->route('admin.language-keyword', ['language' => $request->get('language'), 'group' => $request->get('group'), 'filter' => $request->get('filter')]);
        }

        $languages = $this->translation->allLanguages();
        $groups = $this->translation->getGroupsFor(config('app.key_locale'))->merge('single');

        // Get search term
        $searchTerm = $request->get('search');

        // Apply filter or search term
        $filter = $request->get('filter') ?? $searchTerm;

        $translations = $this->translation->filterTranslationsFor($language, $filter);

        if ($request->has('group') && $request->get('group')) {
            if ($request->get('group') === 'single') {
                $translations = $translations->get('single');
                $translations = new Collection(['single' => $translations]);
            } else {
                $translations = $translations->get('group')->filter(function ($values, $group) use ($request) {
                    return $group === $request->get('group');
                });

                $translations = new Collection(['group' => $translations]);
            }
        }

        return view('backend.language.keyword', compact('language', 'languages', 'groups', 'translations', 'searchTerm'));
    }

    public function languageAppKeyword(Request $request, $locale)
    {
        $filePath = resource_path("lang/app/$locale.json");
        $translations = [];

        if (file_exists($filePath)) {
            $translations = json_decode(file_get_contents($filePath), true);
        }

        // Get search keyword
        $searchTerm = $request->get('search');

        // Apply search
        if ($searchTerm) {
            $translations = Arr::dot($translations);
            $translations = array_filter($translations, function ($value) use ($searchTerm) {
                return Str::contains($value, $searchTerm);
            });
            $translations = Arr::undot($translations);
        }

        return view('backend.language.app-keyword', [
            'locale' => $locale,
            'translations' => Arr::dot($translations),
        ]);
    }

    public function keywordUpdate(Request $request)
    {
        $group = $request->group;
        $language = $request->language;
        $isGroupTranslation = ! Str::contains($group, 'single');

        $this->translation->add($request, $language, $isGroupTranslation);

        notify()->success(__('Keyword updated successfully!'));

        return redirect()->back();
    }

    public function appKeywordUpdate(Request $request)
    {
        $locale = $request->language;
        $key = $request->key;
        $value = $request->value;

        $filePath = resource_path("lang/app/$locale.json");

        if (! file_exists($filePath)) {
            notify()->error(__('Language file not found!'));

            return redirect()->back();
        }

        $translations = json_decode(file_get_contents($filePath), true);

        Arr::set($translations, $key, $value);

        file_put_contents($filePath, json_encode($translations, JSON_PRETTY_PRINT));

        notify()->success(__('Keyword updated successfully!'));

        return redirect()->back();
    }

    public function syncMissing()
    {
        Artisan::call('translation:sync-missing-translation-keys');

        notify()->success(__('Missing translation keys synced successfully!'));

        return redirect()->back();
    }
}
