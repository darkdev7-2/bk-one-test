<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\KYCStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Events\UserReferred;
use App\Facades\Txn\Txn;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\LoginActivities;
use App\Models\ReferralLink;
use App\Models\User;
use App\Rules\RegisterCustomField;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use NotifyTrait;

    public function stepOne(Request $request)
    {
        if (! $this->checkRegistration()) {
            return response()->json([
                'status' => false,
                'message' => __('User registration is closed now'),
            ]);
        }

        $isCountry = (bool) getPageSetting('country_validation');
        $isPhone = (bool) getPageSetting('phone_validation');
        $isReferralCode = (bool) getPageSetting('referral_code_validation');

        $data = $request->validate([
            'country' => [Rule::requiredIf($isCountry), 'string', 'max:255'],
            'phone' => [Rule::requiredIf($isPhone), 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'invite' => [Rule::requiredIf($isReferralCode), 'exists:referral_links,code'],
            'custom_fields_data' => [new RegisterCustomField(true)],
        ], [
            'invite.required' => __('Referral code field is required.'),
            'invite.exists' => __('Referral code is invalid'),
        ]);

        $request->session()->put('step_one', $request->all());

        return response()->json([
            'status' => true,
            'message' => __('Step one completed'),
        ]);
    }

    public function stepTwo(Request $request)
    {
        if (! $this->checkRegistration()) {
            return response()->json([
                'status' => false,
                'message' => __('User registration is closed now'),
            ]);
        }

        $isUsername = (bool) getPageSetting('username_validation') && getPageSetting('username_show');
        $isCountry = (bool) getPageSetting('country_validation') && getPageSetting('country_show');
        $isPhone = (bool) getPageSetting('phone_validation') && getPageSetting('phone_show');
        $isBranch = getPageSetting('branch_validation') && branch_enabled() && getPageSetting('branch_show');

        $isGender = (bool) getPageSetting('gender_validation') && getPageSetting('gender_show');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => [Rule::requiredIf($isGender), 'in:male,female,other'],
            'username' => [Rule::requiredIf($isUsername), 'string', 'max:255', 'unique:users'],
            'branch_id' => [Rule::requiredIf($isBranch), 'exists:branches,id'],
            'i_agree' => ['required'],
        ]);

        $formData = array_merge($request->session()->get('step_one', []), $data);
        $location = getLocation();
        $country = $isCountry ? $formData['country'] : $location->name;

        $phone = $isPhone ? ($isCountry ? $this->getDialCode($country) : $location->dial_code).' '.$formData['phone'] : $location->dial_code.' ';

        try {

            DB::beginTransaction();

            $user = User::create([
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
                'custom_fields_data' => $formData['custom_fields_data'],
                'kyc' => Kyc::where('status', 1)->exists() ? KYCStatus::NOT_SUBMITTED : KYCStatus::Verified,
            ]);

            $shortcodes = [
                '[[full_name]]' => $formData['first_name'].' '.$formData['last_name'],
            ];

            // Notify user and admin
            $this->pushNotify('new_user', $shortcodes, route('admin.user.edit', $user->id), $user->id, 'Admin');
            $this->pushNotify('new_user', $shortcodes, null, $user->id);
            $this->smsNotify('new_user', $shortcodes, $user->phone);

            // Referred event
            $referral = ReferralLink::whereCode(data_get($formData, 'invite'))->first();
            event(new UserReferred($referral?->id, $user));

            if (setting('referral_signup_bonus', 'permission') && (float) setting('signup_bonus', 'fee') > 0) {
                $signupBonus = (float) setting('signup_bonus', 'fee');
                $user->increment('balance', $signupBonus);
                (new Txn)->new($signupBonus, 0, $signupBonus, 'system', 'Signup Bonus', TxnType::SignupBonus, TxnStatus::Success, null, null, $user->id);
            }

            LoginActivities::add($user->id);
            DB::commit();

            $request->session()->forget('step_one');

            return response()->json([
                'status' => true,
                'message' => __('Registered Successfully'),
                'token' => $user->createToken('auth_token')->plainTextToken,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    protected function getDialCode($country)
    {
        $country = collect(getCountries())->first(function ($value, $key) use ($country) {
            return data_get($value, 'code') == $country;
        });

        return data_get($country, 'dial_code', '');
    }

    private function checkRegistration(): bool
    {
        return (bool) setting('account_creation', 'permission');
    }
}
