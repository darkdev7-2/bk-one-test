<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\UserNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserNavigationController extends Controller
{
    public function index()
    {
        $navigations = UserNavigation::orderBy('position')->get();
        $languages = Language::where('status', true)->get();
        $locale = array_column($languages->toArray(), 'locale');
        // translation data for each navigation
        $navigationTranslations = $navigations->mapWithKeys(function ($nav) use ($locale) {
            $translations = $nav->translation ?? [];
            if (is_string($translations)) {
                $translations = json_decode($translations, true);
            }
            $localeKey = array_fill_keys($locale, '');
            $localeContent = array_merge($localeKey, $translations ?? []);

            return [$nav->id => $localeContent];
        });

        return view('backend.user_navigations.index', compact('navigations', 'languages', 'navigationTranslations'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:user_navigations',
            'translation' => 'required|array',
            'translation.en' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return back();
        }

        $translations = $request->input('translation');
        $name = $translations['en'];

        UserNavigation::where('type', $request->string('type'))->update([
            'name' => $name,
            'translation' => $translations,
        ]);

        notify()->success(__('User navigation updated successfully!'), 'Success');

        return back();
    }

    public function positionUpdate(Request $request)
    {
        $ids = $request->except('_token');

        $type = $request->type;

        $navigations = new UserNavigation;
        $i = 1;

        foreach ($ids as $id) {
            $navigation = $navigations->find((int) $id);

            $navigation->update([
                'position' => $i,
            ]);

            $i++;
        }

        notify()->success(__('Menu Draggable Successfully'), 'Success');

        return redirect()->back();
    }
}
