<?php

namespace Payment\Nowpayments;

use App\Facades\Txn\Txn;
use Modules\Payment\Nowpayments\NowPaymentsAPI;
use Payment\Transaction\BaseTxn;

class NowpaymentsTxn extends BaseTxn
{
    protected $secretKey;

    protected $apiKey;

    protected $acEmail, $acPassword;

    public function __construct($txnInfo)
    {
        parent::__construct($txnInfo);
        $credential = gateway_info('nowpayments');
        $this->apiKey = $credential->api_key;
        $this->secretKey = $credential->secret_key;
        $this->acEmail = $credential->email;
        $this->acPassword = $credential->password;

        $fieldData = json_decode($txnInfo->manual_field_data, true);
        $this->toAddress = $fieldData['address']['value'] ?? '';
        $this->currency = $txnInfo?->pay_currency ?? '';

    }

    public function deposit()
    {
        $nowPaymentsAPI = new NowPaymentsAPI($this->apiKey, $this->secretKey);
        $payment = $nowPaymentsAPI->createInvoice([
            'price_amount' => $this->amount,
            'price_currency' => 'USD',
            // 'pay_currency' => $this->currency,
            'order_id' => $this->txn,
            'ipn_callback_url' => route('ipn.nowpayments'),
            'cancel_url' => route('status.cancel'),
            'success_url' => route('status.success'),
        ]);

        return redirect()->to($payment['invoice_url']);
    }
    public function withdraw()
    {
        $nowPaymentsAPI = new NowPaymentsAPI($this->apiKey, $this->secretKey);

        $validateAddress = $nowPaymentsAPI->validatePayoutAddress([
            'address' => $this->toAddress,
            'currency' => strtolower($this->currency),
        ]);

        if (!$validateAddress['status']) {
            notify()->error('Invalid address: ' . $validateAddress['message']);
            $this->makeTrnxFailed();
            return;
        }
        try {
            $payment = $nowPaymentsAPI->createPayout([
                'withdrawals' => [
                    [
                        'amount' => $this->amount,
                        'currency' => strtolower($this->currency),
                        'address' => $this->toAddress,
                    ],
                ],
                'account_email' => $this->acEmail,
                'account_password' => $this->acPassword,
            ]);
        } catch (\Exception $e) {
            $this->makeTrnxFailed();
            notify()->error('Withdraw request failed: ' . $e->getMessage());
            return;
        }

        if($payment['statusCode'] !== 200) {
            $this->makeTrnxFailed();
            notify()->error('Withdraw request failed: ' . $payment['message']);
            return;
        }

        $paymentData = $payment['withdrawals'][0] ?? null;
        
        
        if (in_array(strtolower($paymentData['status']), ['failed', 'rejected']) && $paymentData) {
            $this->makeTrnxFailed();
            notify()->error('Withdraw request failed: ' . $paymentData['error']);
        } else {
            notify()->success('Withdraw request successful: ' . $paymentData['error']);
        }

        return $payment;
    }

    protected function makeTrnxFailed(){
        (new Txn)->update($this->txn, \App\Enums\TxnStatus::Failed, $this->userId);
        notify()->error('Transaction failed');
        return false;
    }
}       
