<?php

namespace App\Http\Controllers\Backend;

use App\Enums\KYCStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Card;
use App\Models\Dps;
use App\Models\Fdr;
use App\Models\Kyc;
use App\Models\LevelReferral;
use App\Models\Loan;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserKyc;
use App\Models\UserWallet;
use App\Rules\RegisterCustomField;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use App\Traits\VirtualCard;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Txn;

class UserController extends Controller
{
    use ImageUpload, NotifyTrait, VirtualCard;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:customer-list|customer-login|customer-mail-send|customer-basic-manage|customer-change-password|all-type-status|customer-balance-add-or-subtract', ['only' => ['index', 'activeUser', 'disabled', 'mailSendAll', 'mailSend']]);
        $this->middleware('permission:customer-basic-manage|customer-change-password|all-type-status|customer-balance-add-or-subtract', ['only' => ['edit']]);
        $this->middleware('permission:customer-login', ['only' => ['userLogin']]);
        $this->middleware('permission:customer-mail-send', ['only' => ['mailSendAll', 'mailSend']]);
        $this->middleware('permission:customer-basic-manage', ['only' => ['update']]);
        $this->middleware('permission:customer-change-password', ['only' => ['passwordUpdate']]);
        $this->middleware('permission:all-type-status', ['only' => ['statusUpdate']]);
        $this->middleware('permission:customer-balance-add-or-subtract', ['only' => ['balanceUpdate']]);
    }

    public function index(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::query()
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('All Customers');

        return view('backend.user.index', compact('users', 'title'));
    }

    public function activeUser(Request $request)
    {

        $search = $request->query('query') ?? null;

        $users = User::active()
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Active Customers');

        return view('backend.user.index', compact('users', 'title'));
    }

    public function disabled(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::disabled()
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Disabled Customers');

        return view('backend.user.index', compact('users', 'title'));
    }

    public function closed(Request $request)
    {
        $search = $request->query('query') ?? null;

        $users = User::closed()
            ->when(! blank(request('email_status')), function ($query) {
                if (request('email_status')) {
                    $query->whereNotNull('email_verified_at');
                } else {
                    $query->whereNull('email_verified_at');
                }
            })
            ->when(! blank(request('kyc_status')), function ($query) {
                $query->where('kyc', request('kyc_status'));
            })
            ->when(! blank(request('status')), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(! blank(request('sort_field')), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            })
            ->search($search)
            ->paginate();

        $title = __('Closed Customers');

        return view('backend.user.index', compact('users', 'title'));
    }

    public function create()
    {
        $kycs = Kyc::where('status', true)->get();

        $branches = Branch::where('status', 1)->get();

        return view('backend.user.create', compact('kycs', 'branches'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $level = LevelReferral::where('type', 'investment')->max('the_order') + 1;

        $earnings = null;
        $dpses = null;
        $transactions = null;
        $fdres = null;
        $loans = null;
        $wallets = [];
        $cards = null;
        $user_wallets = [];
        $tickets = null;

        if (request('tab') == 'paybacks') {
            $earnings = Transaction::where('user_id', $id)
                ->profit()
                ->when(request('query') != null, function ($query) {
                    $query->where('description', 'LIKE', '%'.request('query').'%');
                })
                ->when(request('sort_field') != null, function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'dps') {
            $dpses = Dps::with('plan')
                ->where('user_id', $id)
                ->when(request('query') != null, function ($query) {
                    $query->whereHas('plan', function ($planQuery) {
                        $planQuery->where('name', 'LIKE', '%'.request('query').'%');
                    });
                })
                ->when(in_array(request('sort_field'), ['created_at', 'dps_id', 'per_installment', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(request('sort_field') == 'dps', function ($query) {
                    $query->whereHas('plan', function ($dpsQuery) {
                        $dpsQuery->orderBy('name', request('sort_dir'));
                    });
                })
                ->when(request('sort_field') == 'interest_rate', function ($query) {
                    $query->whereHas('plan', function ($dpsQuery) {
                        $dpsQuery->orderBy('interest_rate', request('sort_dir'));
                    });
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'transactions') {
            $transactions = Transaction::where('user_id', $id)
                ->search(request('query'))
                ->with([
                    'userWallet' => function ($q) {
                        $q->select('id', 'currency_id')->with('currency:id,name');
                    },
                ])
                ->when(request('sort_field') != null, function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'fdr') {
            $fdres = Fdr::with('plan')
                ->where('user_id', $id)
                ->when(request('query') != null, function ($query) {
                    $query->whereHas('plan', function ($planQuery) {
                        $planQuery->where('name', 'LIKE', '%'.request('query').'%');
                    });
                })
                ->when(in_array(request('sort_field'), ['created_at', 'fdr_id', 'amount', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(request('sort_field') == 'fdr', function ($query) {
                    $query->whereHas('plan', function ($dpsQuery) {
                        $dpsQuery->orderBy('name', request('sort_dir'));
                    });
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'loan') {
            $loans = Loan::with('plan')
                ->where('user_id', $id)
                ->when(request('query') != null, function ($query) {
                    $query->whereHas('plan', function ($planQuery) {
                        $planQuery->where('name', 'LIKE', '%'.request('query').'%');
                    });
                })
                ->when(in_array(request('sort_field'), ['created_at', 'loan_no', 'amount', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(request('sort_field') == 'loan', function ($query) {
                    $query->whereHas('plan', function ($dpsQuery) {
                        $dpsQuery->orderBy('name', request('sort_dir'));
                    });
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'ticket') {
            $tickets = Ticket::where('user_id', $id)
                ->when(request('query') != null, function ($query) {
                    $query->where('title', 'LIKE', '%'.request('query').'%');
                })
                ->when(in_array(request('sort_field'), ['created_at', 'title', 'status']), function ($query) {
                    $query->orderBy(request('sort_field'), request('sort_dir'));
                })
                ->when(! request()->has('sort_field'), function ($query) {
                    $query->latest();
                })
                ->paginate()
                ->withQueryString();
        } elseif (request('tab') == 'card') {
            $cards = Card::currentUser($id)->with('cardHolder')->latest()->get();
        }

        $branches = Branch::where('status', 1)->get();

        $statistics = [
            'total_deposit' => $user->total_deposit,
            'total_fund_transfer' => $user->totalTransfer(),
            'total_dps' => $user->dps->sum('total_dps_amount'),
            'total_fdr' => $user->fdr->sum('amount'),
            'total_loan' => $user->loan->sum('amount'),
            'total_bill' => $user->bill->sum('amount'),
            'total_withdraw' => $user->totalWithdraw(),
            'total_tickets' => $user->tickets->count(),
            'points' => $user->points,
            'paybacks' => $user->totalProfit(),
            'all_referral' => $user->referrals()->count(),
        ];

        // Wallets
        if (setting('multiple_currency', 'permission')) {
            [$wallets, $user_wallets] = $this->userWallets($user);
        }

        return view('backend.user.edit', [
            'user' => $user,
            'level' => $level,
            'branches' => $branches,
            'statistics' => $statistics,
            'earnings' => $earnings,
            'transactions' => $transactions,
            'tickets' => $tickets,
            'dpses' => $dpses,
            'fdres' => $fdres,
            'loans' => $loans,
            'wallets' => $wallets,
            'user_wallets' => $user_wallets,
            'cards' => $cards,
        ]);
    }

    protected function userWallets($user)
    {
        $default_wallet = [
            [
                'id' => 0,
                'name' => __('Default Wallet'),
                'balance' => $user->balance,
                'code' => setting('site_currency', 'global'),
                'symbol' => setting('currency_symbol', 'global'),
            ],
        ];
        $user_wallets = $user->wallets->load('currency')->map(function ($wallet) {
            return [
                'id' => $wallet->id,
                'name' => $wallet->currency->name,
                'balance' => $wallet->balance,
                'code' => $wallet->currency->code,
                'symbol' => $wallet->currency->symbol,
            ];
        });

        $wallets = array_merge($default_wallet, $user_wallets->toArray());

        return [
            $wallets,
            $user_wallets,
        ];
    }

    /**
     * @return RedirectResponse
     */
    public function statusUpdate($id, Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'email_verified' => 'required',
            'kyc' => 'required',
            'two_fa' => 'required',
            'deposit_status' => 'required',
            'withdraw_status' => 'required',
            'transfer_status' => 'required',
            'otp_status' => 'required',
            'dps_status' => 'required',
            'fdr_status' => 'required',
            'loan_status' => 'required',
            'portfolio_status' => 'required',
            'reward_status' => 'required',
            'referral_status' => 'required',
            'pay_bill_status' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $data = [
            'status' => $input['status'],
            'kyc' => $input['kyc'],
            'two_fa' => $input['two_fa'],
            'deposit_status' => $input['deposit_status'],
            'withdraw_status' => $input['withdraw_status'],
            'transfer_status' => $input['transfer_status'],
            'otp_status' => $input['otp_status'],
            'dps_status' => $input['dps_status'],
            'fdr_status' => $input['fdr_status'],
            'loan_status' => $input['loan_status'],
            'portfolio_status' => $input['portfolio_status'],
            'reward_status' => $input['reward_status'],
            'referral_status' => $input['referral_status'],
            'pay_bill_status' => $input['pay_bill_status'],
            'email_verified_at' => $input['email_verified'] == 1 ? now() : null,
        ];

        $user = User::find($id);

        if ($user->status != $input['status'] && ! $input['status']) {

            $shortcodes = [
                '[[full_name]]' => $user->full_name,
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => route('home'),
            ];

            $this->mailNotify($user->email, 'user_account_disabled', $shortcodes);
            $this->smsNotify('user_account_disabled', $shortcodes, $user->phone);
        }

        User::find($id)->update($data);

        if (! $input['kyc']) {
            $this->markAsUnverified($id);
        }

        notify()->success('Status Updated Successfully', 'Success');

        return redirect()->back();
    }

    protected function markAsUnverified($user_id)
    {
        UserKyc::where('user_id', $user_id)->where('is_valid', true)->update([
            'is_valid' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'date_of_birth' => 'nullable|date',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'custom_fields_data' => [new RegisterCustomField],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back()->withInput();
        }

        $generateUsername = $request->fname.$request->lname;

        $usernameExists = User::where('username', $generateUsername)->exists();

        $registerCustomFields = json_decode(getPageSetting('register_custom_fields'), true);

        $input = $request->all();
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

        $user = User::create([
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'username' => ! $usernameExists ? strtolower($generateUsername) : strtolower($generateUsername.Str::random(4)),
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'branch_id' => $request->get('branch_id'),
            'gender' => $request->gender,
            'date_of_birth' => $request->date('date_of_birth'),
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'password' => bcrypt($request->password),
            'status' => 1,
            'two_fa' => 0,
            'deposit_status' => 1,
            'withdraw_status' => 1,
            'transfer_status' => 4,
            'otp_status' => 0,
            'dps_status' => 1,
            'fdr_status' => 1,
            'loan_status' => 1,
            'portfolio_status' => 1,
            'reward_status' => 1,
            'referral_status' => 1,
            'email_verified_at' => now(),
            'kyc_credential' => null,
            'kyc' => KYCStatus::Pending,
            'custom_fields_data' => $input['custom_fields_data'] ?? [],
        ]);

        $kycs = $request->kyc_credential;

        if (is_array($kycs) && is_array($request->kyc_ids)) {

            foreach ($kycs as $id => $kyc) {
                if (is_array($kyc)) {
                    foreach ($kyc as $key => $value) {
                        if (is_file($value)) {
                            $kycs[$id][$key] = self::imageUploadTrait($value);
                        }
                    }
                }
            }

            foreach ($request->kyc_ids as $id) {

                $kyc = Kyc::find($id);

                UserKyc::create([
                    'user_id' => $user->id,
                    'kyc_id' => $kyc->id,
                    'type' => $kyc->name,
                    'data' => $kycs[$id],
                    'is_valid' => true,
                    'status' => 'pending',
                ]);
            }
        }

        notify()->success(__('Customer added successfully!'), 'Success');

        return to_route('admin.user.edit', $user->id);
    }

    /**
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        if ($request->date('date_of_birth') == null) {
            $request->merge(['date_of_birth' => null]);
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username,'.$id,
            'custom_fields_data' => [new RegisterCustomField(true)],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }
        $user = User::findOrFail($id);

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
                    $input['custom_fields_data'][$field['name']] = $user->custom_fields_data[$field['name']] ?? null;
                }
            }
        }
        $input['custom_fields_data'] = $input['custom_fields_data'] ?? [];

        $user->update($input);
        notify()->success('User Info Updated Successfully', 'Success');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function passwordUpdate($id, Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $password = $validator->validated();

        User::find($id)->update([
            'password' => Hash::make($password['new_password']),
        ]);
        notify()->success('User Password Updated Successfully', 'Success');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse|void
     */
    public function balanceUpdate($id, Request $request)
    {
        $isEnabledWallet = setting('multiple_currency', 'permission');
        $validation = [
            'amount' => 'required',
            'type' => 'required',
        ];

        // Wallet Type Validation if multiple currency enabled
        if ($isEnabledWallet) {
            $validation['wallet_type'] = 'required';
        }
        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {
            $amount = $request->amount;
            $type = $request->type;
            $user = User::find($id);
            $adminUser = \Auth::user();

            if ($isEnabledWallet) {
                // if multiple currency enabled then wallet type is required
                $wallet_type = $request->wallet_type;
                if ($wallet_type == 0) {
                    $wallet_name = 'default';
                } else {
                    $user_wallet = UserWallet::find($wallet_type);
                    $wallet_name = $user_wallet?->currency?->name;
                }
            } else {
                // if multiple currency disabled then wallet type is default
                $wallet_type = 0;
                $wallet_name = 'default';
            }

            if ($type == 'add') {
                if ($wallet_type == 0) {
                    $user->balance += $amount;
                    $user->save();
                } else {
                    $user_wallet->balance += $amount;
                    $user_wallet->save();
                }

                // Txn::new($amount, 0, $amount, 'system', 'Money added in '.ucwords($wallet_name).' Wallet from System', TxnType::Deposit, TxnStatus::Success, null, null, $id, $adminUser->id, 'Admin');
                Txn::new(
                    amount: $amount,
                    charge: 0,
                    final_amount: $amount,
                    method: 'system',
                    description: 'Money added in '.ucwords($wallet_name).' Wallet from System',
                    type: TxnType::Deposit,
                    status: TxnStatus::Success,
                    payCurrency: $wallet_name,
                    payAmount: null,
                    userID: $id,
                    relatedUserID: $adminUser->id,
                    relatedModel: 'Admin',
                    walletType: $wallet_type
                );

                $status = 'Success';
                $message = __('Balance added successfully!');
            } elseif ($type == 'subtract') {
                if ($wallet_type == 0) {
                    $user->balance -= $amount;
                    $user->save();
                } else {
                    $user_wallet->balance -= $amount;
                    $user_wallet->save();
                }

                // Txn::new($amount, 0, $amount, 'system', 'Money subtract in '.ucwords($wallet_name).' Wallet from System', TxnType::Subtract, TxnStatus::Success, null, null, $id, $adminUser->id, 'Admin');

                Txn::new(
                    amount: $amount,
                    charge: 0,
                    final_amount: $amount,
                    method: 'system',
                    description: 'Money subtract in '.ucwords($wallet_name).' Wallet from System',
                    type: TxnType::Subtract,
                    status: TxnStatus::Success,
                    payCurrency: $wallet_name,
                    payAmount: null,
                    userID: $id,
                    relatedUserID: $adminUser->id,
                    relatedModel: 'Admin',
                    walletType: $wallet_type
                );

                $status = 'Success';
                $message = __('Balance subtracted successfully!');
            }

            notify()->success($message, $status);

            return redirect()->back();
        } catch (Exception $e) {
            $status = 'warning';
            $message = __('something is wrong');
            $code = 503;
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function mailSendAll()
    {
        return view('backend.user.mail_send_all');
    }

    /**
     * @return RedirectResponse
     */
    public function mailSend(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {

            $input = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $shortcodes = [
                '[[subject]]' => $input['subject'],
                '[[message]]' => $input['message'],
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => route('home'),
            ];

            if (isset($request->id)) {
                $user = User::find($request->id);

                $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                $this->mailNotify($user->email, 'user_mail', $shortcodes);
            } else {
                $users = User::where('status', 1)->get();

                foreach ($users as $user) {
                    $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                    $this->mailNotify($user->email, 'user_mail', $shortcodes);
                }
            }
            $status = 'Success';
            $message = __('Mail Send Successfully');
        } catch (Exception $e) {

            $status = 'warning';
            $message = __('Sorry, something is wrong');
        }

        notify()->$status($message, $status);

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function userLogin($id)
    {
        Auth::guard('web')->loginUsingId($id);

        return redirect()->route('user.dashboard');
    }

    public function destroy($id)
    {

        if (config('app.demo')) {
            notify()->warning(__('You cannot change anything in this demo version'));

            return redirect()->back();
        }

        try {

            $user = User::find($id);
            $user->kycs()->delete();
            $user->transaction()->delete();
            $user->dps()->delete();
            $user->bill()->delete();
            $user->fdr()->delete();
            $user->loan()->delete();
            $user->ticket()->delete();
            $user->activities()->delete();
            $user->messages()->delete();
            $user->notifications()->delete();
            $user->refferelLinks()->delete();
            $user->withdrawAccounts()->delete();
            $user->delete();

            notify()->success(__('User deleted successfully'), 'Success');

            return to_route('admin.user.index');
        } catch (\Throwable $th) {
            notify()->error(__('Sorry, something went wrong!'), 'Error');

            return redirect()->back();
        }
    }

    public function updateCardStatus($card_id)
    {

        $card = Card::where('card_id', $card_id)->firstOrFail();

        try {
            // update card status
            $this->cardProviderMap($card->provider)->updateCardStatus($card);

            // Notify user and redirect back
            notify()->success(__('Card status updated successfully'));

            return back();
        } catch (\Throwable $th) {
            notify()->error($th->getMessage());

            return back();
        }
    }

    public function cardBalanceUpdate(Request $request, Card $card)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            // Validate request data
            $balance_amount = $card->amount + $request->amount;

            // update stripe card balance
            $this->cardProviderMap($card->provider)->addCardBalance($card, $balance_amount);

            // Notify user and redirect back
            notify()->success(__('Card balance updated successfully'));

            return back();
        } catch (\Throwable $th) {
            notify()->error($th->getMessage());

            return back();
        }
    }
}
