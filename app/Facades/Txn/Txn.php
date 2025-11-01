<?php

namespace App\Facades\Txn;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\RewardTrait;
use App\Traits\VirtualCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Remotelywork\Installer\Repository\App;

class Txn
{
    use RewardTrait, VirtualCard;

    /**
     * @param  null  $payCurrency
     * @param  null  $payAmount
     * @param  null  $userID
     * @param  null  $fromUserID
     * @param  string  $fromModel
     * @param  array  $manualDepositData
     */
    public function new($amount, $charge, $final_amount, $method, $description, string|TxnType $type, string|TxnStatus $status = TxnStatus::Pending, $payCurrency = null, $payAmount = null, $userID = null, $relatedUserID = null, $relatedModel = 'User', array $manualFieldData = [], $walletType = 'default', $card_id = null, string $approvalCause = 'none', $targetId = null, $targetType = null, $isLevel = false): Transaction
    {
        if ($type === 'withdraw') {
            self::withdrawBalance($amount);
        }
        $transaction = new Transaction;
        $transaction->user_id = $userID ?? Auth::user()->id;
        $transaction->from_user_id = $relatedUserID;
        $transaction->from_model = $relatedModel;
        $transaction->tnx = 'TRX'.strtoupper(Str::random(10));
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->charge = $charge;
        $transaction->final_amount = $final_amount;
        $transaction->method = $method;
        $transaction->pay_currency = $payCurrency;
        $transaction->pay_amount = $payAmount;
        $transaction->manual_field_data = json_encode($manualFieldData);
        $transaction->approval_cause = $approvalCause;
        $transaction->target_id = $targetId;
        $transaction->target_type = $targetType;
        $transaction->is_level = $isLevel;
        $transaction->status = $status;
        $transaction->wallet_type = $walletType;
        $transaction->card_id = $card_id;
        $transaction->save();

        if ($transaction->status === TxnStatus::Success) {
            self::rewardToUser($transaction->user_id, $transaction->id);
        }

        return $transaction;
    }

    public static function transfer($amount, $charge, $final_amount, $description, string|TxnType $type, string|TxnStatus $status, $payCurrency, $payAmount, $userID, $relatedUserID, $relatedModel, $beneficiaryId, $bank_id, $purpose, $transferType, array $manualFieldData = [], $walletType = 'default')
    {
        $transaction = new Transaction;
        $transaction->user_id = $userID ?? Auth::user()->id;
        $transaction->from_user_id = $relatedUserID;
        $transaction->from_model = $relatedModel;
        $transaction->tnx = 'TRX'.strtoupper(Str::random(10));
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->charge = $charge;
        $transaction->final_amount = $final_amount;
        $transaction->pay_currency = $payCurrency;
        $transaction->pay_amount = $payAmount;
        $transaction->beneficiery_id = $beneficiaryId;
        $transaction->bank_id = $bank_id;
        $transaction->status = $status;
        $transaction->purpose = $purpose;
        $transaction->transfer_type = $transferType;
        $transaction->manual_field_data = json_encode($manualFieldData);
        $transaction->wallet_type = $walletType;
        $transaction->save();

        if ($transaction->status === TxnStatus::Success) {
            self::rewardToUser($transaction->user_id, $transaction->id);
        }

        return $transaction;
    }

    private function withdrawBalance($amount): void
    {
        User::find(auth()->user()->id)->removeMoney($amount);
    }

    public function update($tnx, $status, $userId = null, $approvalCause = 'none')
    {
        $transaction = Transaction::tnx($tnx);

        $uId = $userId == null ? auth()->user()->id : $userId;

        $user = User::find($uId);

        if ($status == TxnStatus::Success && App::initApp() && ($transaction->type == TxnType::Deposit || $transaction->type == TxnType::ManualDeposit)) {
            // Default wallet
            if ($transaction->wallet_type == 'default') {
                $user->increment('balance', $transaction->amount);
            } else {
                $user_wallet = UserWallet::find($transaction->wallet_type);

                if ($user_wallet) {
                    $user_wallet->increment('balance', $transaction->pay_amount);
                }
            }
        }

        if ($status == TxnStatus::Failed && $transaction->type == TxnType::WithdrawAuto) {
            $amount = $transaction->amount;
            $user->increment('balance', $transaction->final_amount);
        }

        if ($status == TxnStatus::Success && App::initApp() && ($transaction->type == TxnType::CardDeposit)) {
            $card = $transaction->card;
            $total_amount = $transaction->amount;

            $charge = setting('card_topup_charge_type', 'virtual_card') == 'percentage' ? ((setting('card_topup_charge', 'virtual_card') / 100) * $total_amount) : setting('card_topup_charge', 'virtual_card');

            // create transaction for card topup
            Txn::new($charge, 0, $charge, 'System', 'Card Topup Charge', TxnType::CardLoad, TxnStatus::Success, 'USD', $charge, auth()->id(), null, 'User', $manualData ?? [], 'default');

            // update card balance
            $this->cardProviderMap($card->provider)->addCardBalance($card, $total_amount);
        }

        $data = [
            'status' => $status,
            'approval_cause' => $approvalCause,
        ];

        if ($status == TxnStatus::Success) {
            $this->rewardToUser($uId, $transaction->id);
        }

        $transaction = $transaction->update($data);

        return $transaction;
    }
}
