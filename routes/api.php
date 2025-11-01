<?php

use App\Http\Controllers\Api\Auth\EmailVerificatinController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\TwoFactorController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\DpsController;
use App\Http\Controllers\Api\FdrController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WireTransferController;
use App\Http\Controllers\Api\WithdrawAccountController;
use App\Http\Controllers\Api\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Login
Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

// Register
Route::controller(RegisterController::class)->group(function () {
    Route::post('register/step1', 'stepOne');
    Route::post('register/step2', 'stepTwo');
});

// Forgot Password
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::post('forgot-password', 'sendResetLinkEmail');
    Route::post('reset-verify-otp', 'verifyOtp');
    Route::post('reset-password', 'resetPassword');
});

// Language
Route::get('change-language/{locale}', [LanguageController::class, 'changeLanguage']);

// Countries
Route::controller(GeneralController::class)->group(function () {
    Route::get('get-countries', 'getCountries');
    Route::get('get-branches', 'getBranches');
    Route::get('get-currencies', 'getCurrencies');
    Route::get('get-settings', 'getSettings');
    Route::get('get-banks', 'getBanks');
    Route::get('get-languages', 'getLanguages');
    Route::get('get-register-fields', 'getRegisterFields');
    Route::get('get-onboarding-screen-images', 'getOnboardingScreenImages');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('get-plugins', 'getPlugins');
        Route::get('get-navigations', 'getNavigations');
        Route::get('get-transaction-types', 'getTransactionTypes');
        Route::get('wire-transfer-settings', 'wireTransferSettings');
        Route::get('get-account/{account_id}', 'getAccounts');
        Route::get('get-bill-countries/{type}', 'getBillCountries');
        Route::get('get-card-providers', 'getCardProviders');
        Route::get('get-withdraw-methods', 'getWithdrawMethods');
        Route::get('get-notifications', 'getNotifications');
    });
});

Route::middleware('auth:sanctum', 'isActive')->group(function () {
    // Email Verification
    Route::controller(EmailVerificatinController::class)->group(function () {
        Route::post('send-verify-email', 'sendVerifyEmail');
    });

    // Two Factor Verify
    Route::post('2fa/verify', TwoFactorController::class);

    // FCM Notification
    Route::controller(NotificationController::class)->group(function () {
        Route::post('setup-fcm', 'registerDevice');
    });

    // Get user info
    Route::get('/user', function (Request $request) {
        $user = auth()->user();

        return [
            ...$user->toArray(),
            '2fa_intitialized' => $user->google2fa_secret ? true : false,
            'is_unread_notification' => $user->notifications()->where('read', 0)->count() > 0,
            '2fa_qr_code' => app('pragmarx.google2fa')->getQRCodeInline(setting('site_title', 'global'), $user->email, $user->google2fa_secret),
        ];
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard-data', 'dashboard');
        Route::get('statistics', 'statistics');
        Route::get('transactions', 'transactions');
        Route::get('mark-as-read-notification', 'markNotification');
    });

    // KYC
    Route::get('kyc-histories', [KycController::class, 'histories']);
    Route::apiResource('kyc', KycController::class)->only('index', 'store', 'show');

    // Deposits
    Route::apiResource('deposit', DepositController::class)->only('index', 'store');

    // Wallets
    Route::apiResource('wallets', WalletController::class)->only('index', 'store', 'destroy');

    // Beneficiary
    Route::apiResource('beneficiary', BeneficiaryController::class)->only('index', 'show', 'update', 'store', 'destroy');

    // Transfer
    Route::apiResource('transfer', TransferController::class)->only('index', 'store');

    // Wire Transfer
    Route::post('wire-transfer', WireTransferController::class);

    // DPS
    Route::apiResource('dps', DpsController::class)->only('index', 'store', 'destroy');
    Route::post('dps/increase', [DpsController::class, 'increment']);
    Route::post('dps/decrease', [DpsController::class, 'decrement']);
    Route::get('dps/history', [DpsController::class, 'history']);
    Route::get('dps/details/{dps_id}', [DpsController::class, 'details']);
    Route::get('dps/installments/{dps_id}', [DpsController::class, 'installments']);

    // FDR
    Route::apiResource('fdr', FdrController::class)->only('index', 'store', 'destroy');
    Route::post('fdr/increase', [FdrController::class, 'increment']);
    Route::post('fdr/decrease', [FdrController::class, 'decrement']);
    Route::get('fdr/history', [FdrController::class, 'history']);
    Route::get('fdr/details/{fdr_id}', [FdrController::class, 'details']);
    Route::get('fdr/installments/{fdr_id}', [FdrController::class, 'installments']);

    // Loan
    Route::get('loan/plans', [LoanController::class, 'plans']);
    Route::post('loan/subscribe', [LoanController::class, 'subscribe']);
    Route::get('loan/history', [LoanController::class, 'history']);
    Route::get('loan/details/{loan_id}', [LoanController::class, 'details']);
    Route::post('loan/cancel', [LoanController::class, 'cancel']);
    Route::get('loan/installments/{loan_id}', [LoanController::class, 'installments']);
    Route::post('loan/pay-installment', [LoanController::class, 'payInstallment']);

    // Pay Bill
    Route::post('pay-bill', [BillController::class, 'payNow'])->middleware('appDemo');
    Route::get('pay-bill/history', [BillController::class, 'history']);
    Route::get('pay-bill/services/{country}/{type}', [BillController::class, 'getServices']);

    // Cards
    Route::apiResource('cards', CardController::class)->only('index', 'store', 'show', 'destroy');
    Route::post('cards/balance/topup/{id}', [CardController::class, 'topupBalance']);
    Route::post('cards/update-status/{card_id}', [CardController::class, 'updateStatus']);
    Route::get('cards/transactions/{card_id}', [CardController::class, 'transactions']);
    Route::get('cardholders', [CardController::class, 'cardholders']);

    // Referral
    Route::prefix('referral')->controller(ReferralController::class)->group(function () {
        Route::get('info', 'index');
        Route::get('direct', 'directReferrals');
        Route::get('tree', 'referralTree');
    });

    // Portfolio
    Route::get('portfolio', PortfolioController::class);

    // Rewards
    Route::get('rewards', [RewardController::class, 'index']);
    Route::post('rewards/redeem', [RewardController::class, 'redeem']);

    // Ticket
    Route::apiResource('ticket', TicketController::class)->except('update', 'destroy');
    Route::post('ticket/reply/{uuid}', [TicketController::class, 'reply']);
    Route::post('ticket/action/{uuid}', [TicketController::class, 'action']);

    // Withdraw Account
    Route::apiResource('withdraw-account', WithdrawAccountController::class)->only('index', 'store', 'update', 'destroy');
    Route::post('withdraw', WithdrawController::class);

    // Settings
    Route::prefix('settings')->controller(SettingsController::class)->middleware('appDemo')->group(function () {
        Route::post('profile', 'profileUpdate');
        Route::post('2fa/{type}', 'twoFa');
        Route::post('passcode', 'passcode');
        Route::post('passcode/change', 'changePasscode');
        Route::post('passcode/disable', 'disablePasscode');
        Route::post('passcode/verify', 'verifyPasscode');
        Route::post('account-close', 'accountClose');
        Route::post('change-password', 'updatePassword');
    });
});
