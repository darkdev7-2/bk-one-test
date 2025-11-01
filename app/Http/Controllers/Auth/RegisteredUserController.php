<?php

namespace App\Http\Controllers\Auth;

use App\Enums\KYCStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Events\UserReferred;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Kyc;
use App\Models\LoginActivities;
use App\Models\Page;
use App\Models\ReferralLink;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Rules\RegisterCustomField;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Txn;

class RegisteredUserController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function step1()
    {
        if (! setting('account_creation', 'permission')) {
            abort('403', 'User registration is closed now');
        }
        $page = Page::where('code', 'registration')->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::where('code', 'registration')->where('locale', defaultLocale())->first();
        }
        $data = json_decode($page?->data, true);

        $location = getLocation();
        $referralCode = ReferralLink::find(request()->cookie('invite'))?->code;

        return view('frontend::auth.register', compact('location', 'referralCode', 'data'));
    }

    public function step1Store(Request $request)
    {
        $isCountry = (bool) getPageSetting('country_validation') && getPageSetting('country_show');
        $isPhone = (bool) getPageSetting('phone_validation') && getPageSetting('phone_show');
        $isReferralCode = (bool) getPageSetting('referral_code_validation') && getPageSetting('referral_code_show');

        $request->validate([
            'country' => [Rule::requiredIf($isCountry), 'string', 'max:255'],
            'phone' => [Rule::requiredIf($isPhone), 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'invite' => [Rule::requiredIf($isReferralCode), 'exists:referral_links,code'],
            'custom_fields_data' => [new RegisterCustomField],
        ], [
            'invite.required' => __('Referral code field is required.'),
            'invite.exists' => __('Referral code is invalid'),
        ]);
        $input = $request->all();
        $registerCustomFields = json_decode(getPageSetting('register_custom_fields'), true);
        if ($registerCustomFields) {
            foreach ($registerCustomFields as $key => $field) {
                if (isset($input['custom_fields_data'][$field['name']])) {
                    if (in_array($field['type'], ['file', 'camera'])) {
                        $input['custom_fields_data'][$field['name']] = $this->imageUploadTrait($request->file('custom_fields_data.'.$field['name'].''));
                    } else {
                        $input['custom_fields_data'][$field['name']] = $request->{'custom_fields_data.'.$field['name']};
                    }
                } else {
                    $input['custom_fields_data'][$field['name']] = null;
                }
            }
        }

        Session::put('step1', $input);

        return redirect()->route('register.step2');
    }

    /**
     * Handle an incoming registration request.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {

        $isUsername = (bool) getPageSetting('username_validation') && getPageSetting('username_show');
        $isCountry = (bool) getPageSetting('country_validation') && getPageSetting('country_show');
        $isPhone = (bool) getPageSetting('phone_validation') && getPageSetting('phone_show');
        $isBranch = getPageSetting('branch_validation') && branch_enabled() && getPageSetting('branch_show');

        $isGender = (bool) getPageSetting('gender_validation') && getPageSetting('gender_show');

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'g-recaptcha-response' => Rule::requiredIf(plugin_active('Google reCaptcha')),
            new Recaptcha,
            'gender' => [Rule::requiredIf($isGender), 'in:Male,Female,Others'],
            'username' => [Rule::requiredIf($isUsername), 'string', 'max:255', 'unique:users'],
            'branch_id' => [Rule::requiredIf($isBranch), 'exists:branches,id'],
            'i_agree' => ['required'],
            'custom_fields_data' => [new RegisterCustomField(true)],
        ]);

        $input = $request->all();

        $formData = array_merge(Session::get('step1', []), $input);
        $location = getLocation();
        $phone = $isPhone ? ($isCountry ? explode(':', $formData['country'])[1] : $location->dial_code).' '.$formData['phone'] : $location->dial_code.' ';
        $country = $isCountry ? explode(':', $formData['country'])[0] : $location->name;

        $regiData = [
            'portfolio_id' => null,
            'portfolios' => json_encode([]),
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
            'branch_id' => $request->get('branch_id'),
            'gender' => $request->get('gender', ''),
            'username' => $isUsername ? $formData['username'] : $formData['first_name'].$formData['last_name'].rand(1000, 9999),
            'country' => $country,
            'phone' => $phone,
            'email' => $formData['email'],
            'password' => Hash::make($formData['password']),
            'custom_fields_data' => data_get($formData, 'custom_fields_data', []),
            'kyc' => Kyc::where('status', 1)->exists() ? KYCStatus::NOT_SUBMITTED : KYCStatus::Verified,

        ];
        $user = User::create($regiData);

        $shortcodes = [
            '[[full_name]]' => $formData['first_name'].' '.$formData['last_name'],
        ];

        // Notify user and admin
        $this->pushNotify('new_user', $shortcodes, route('admin.user.edit', $user->id), $user->id, 'Admin');
        $this->pushNotify('new_user', $shortcodes, null, $user->id);
        $this->smsNotify('new_user', $shortcodes, $user->phone);

        // Referred event
        event(new UserReferred($request->cookie('invite'), $user));

        if (setting('referral_signup_bonus', 'permission') && (float) setting('signup_bonus', 'fee') > 0) {
            $signupBonus = (float) setting('signup_bonus', 'fee');
            $user->increment('balance', $signupBonus);
            Txn::new($signupBonus, 0, $signupBonus, 'system', 'Signup Bonus', TxnType::SignupBonus, TxnStatus::Success, null, null, $user->id);
            Session::put('signup_bonus', $signupBonus);
        }

        Cookie::forget('invite');
        Auth::login($user);
        LoginActivities::add();

        $request->session()->put('newly_registered', true);

        return to_route('register.final');
    }

    /**
     * Display the registration view.
     *
     * @return View
     */
    public function create()
    {
        if (! setting('account_creation', 'permission')) {
            notify()->warning(__('User registration is closed now.'));

            return to_route('home');
        }

        $page = Page::where('code', 'registration')->where('locale', app()->getLocale())->first();

        if (! $page) {
            $page = Page::where('code', 'registration')->where('locale', defaultLocale())->first();
        }
        $data = json_decode($page?->data, true);

        $googleReCaptcha = plugin_active('Google reCaptcha');
        $location = getLocation();
        $branches = Branch::where('status', 1)->get();

        return view('frontend::auth.register2', compact('location', 'googleReCaptcha', 'data', 'branches'));
    }

    public function final()
    {
        if (! request()->session()->has('newly_registered')) {
            return to_route('user.dashboard');
        }

        request()->session()->forget('newly_registered');

        return view('frontend::auth.final');
    }
}
