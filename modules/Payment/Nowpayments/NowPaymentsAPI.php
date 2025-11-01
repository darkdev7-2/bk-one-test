<?php

namespace Modules\Payment\Nowpayments;

use InvalidArgumentException;

class NowPaymentsAPI
{
    private $session;

    private $token;

    const API_BASE = 'https://api.nowpayments.io/v1/';
    // const API_BASE = 'https://api-sandbox.nowpayments.io/v1/';

    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new Exception('API key is not specified');
        }
        $this->token = $token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $this->session = $ch;
    }

    private function Call($method, $endpoint, $data = [])
    {
        $ch = $this->session;

        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $this->token]);
                if (!empty($data)) {
                    if (is_array($data)) {
                        $parameters = http_build_query($data);
                        curl_setopt($ch, CURLOPT_URL, self::API_BASE . $endpoint . '?' . $parameters);
                    } else {
                        if ($endpoint == 'payment') {
                            curl_setopt($ch, CURLOPT_URL, self::API_BASE . $endpoint . '/' . $data);
                        }
                    }
                } else {
                    curl_setopt($ch, CURLOPT_URL, self::API_BASE . $endpoint);
                }
                break;

            case 'POST':
                $headers = ['X-API-KEY: ' . $this->token, 'Content-Type: application/json'];
                if(isset($data['token']) && !empty($data['token'])) {
                    $headers[] = 'Authorization: Bearer ' . $data['token'];
                    unset($data['token']);
                }
                $data = json_encode($data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_URL, self::API_BASE . $endpoint);
                break;

            default:
                break;
        }

        $response = curl_exec($ch);

        if($response == 'OK'){
            return ['status' => true, 'message' => 'OK'];
        }
        return json_decode($response, true);
    }

    public function status()
    {
        return $this->Call('GET', 'status');
    }

    public function getCurrencies()
    {
        return $this->Call('GET', 'currencies');
    }

    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'amount'				=> (int|float) Required.
     *                         'currency_from'		=> (string) Required.
     *                         'currency_to'		=> (string) Required.
     *                         ]
     */
    public function getEstimatePrice(array $params)
    {
        return $this->Call('GET', 'estimate', $params);
    }

    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'price_amount'			=> (int|float) Required. The fiat equivalent of the price to be paid in crypto.
     *                         'price_currency'			=> (string) Required. The fiat currency in which the price_amount is specified (usd, eur, etc)
     *                         'pay_currency'			=> (string) Required. The crypto currency in which the pay_amount is specified (btc, eth, etc)
     *                         'pay_amount'				=> (int|float) Optional. The amount that users have to pay for the order stated in crypto
     *                         'ipn_callback_url'		=> (string) Optional. URL to receive callbacks, should contain "http" or "https", eg. "https://nowpayments.io"
     *                         'order_id'				=> (string) Optional. Inner store order ID, e.g. "RGDBP-21314"
     *                         'order_description'		=> (string) Optional. Inner store order description, e.g. "Apple Macbook Pro 2019 x 1"
     *                         'purchase_id'			=> (string) Optional. ID of purchase for which you want to create aother payment, only used for several payments for one order
     *                         'payout_address'			=> (string) Optional. Usually the funds will go to the address you specify in your Personal account
     *                         'payout_currency'		=> (string) Optional. Currency of your external payout_address, required when payout_adress is specified
     *                         'payout_extra_id'		=> (string) Optional. Extra ID or memo or tag for external payout_address
     *                         'fixed_rate'				=> (bool) Optional. Required for fixed-rate exchanges
     *                         ]
     */
    public function createPayment(array $params)
    {
        return $this->Call('POST', 'payment', $params);
    }

    /**
     * @param  int  $paymentID  Required. ID of created payment
     */
    public function getPaymentStatus(int $paymentID)
    {
        return $this->Call('GET', 'payment', $paymentID);
    }

    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'currency_from'		=> (string) Required.
     *                         'currency_to'		=> (string) Required.
     *                         ]
     */
    public function getMinimumPaymentAmount(array $params)
    {
        return $this->Call('GET', 'min-amount', $params);
    }

    /**
     * @param  array  $params  Array of options, all values are optional
     *                         $params = [
     *                         'limit'			=> (int|string) number of records in one page. (possible values: from 1 to 500)
     *                         'page'			=> (int|string) the page number you want to get (possible values: from 0 to page count - 1)
     *                         'sortBy'			=> (string) sort the received list by a paramenter. Set to created_at by default (possible values: payment_id, payment_status, pay_address, price_amount, price_currency, pay_amount, actually_paid, pay_currency, order_id, order_description, purchase_id, outcome_amount, outcome_currency)
     *                         'orderBy'		=> (string) display the list in ascending or descending order. Set to asc by default (possible values: asc, desc)
     *                         'dateFrom'		=> (string) select the displayed period start date (date format: YYYY-MM-DD or yy-MM-ddTHH:mm:ss.SSSZ)
     *                         'dateTo'			=> (string) select the displayed period end date (date format: YYYY-MM-DD or yy-MM-ddTHH:mm:ss.SSSZ)
     *                         ]
     */
    public function getListPayments(array $params = [])
    {
        return $this->Call('GET', 'payment', $params);
    }

    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'price_amount'			=> (int|float) Required. The fiat equivalent of the price to be paid in crypto.
     *                         'price_currency'			=> (string) Required. The fiat currency in which the price_amount is specified (usd, eur, etc)
     *                         'pay_currency'			=> (string) Required. The crypto currency in which the pay_amount is specified (btc, eth, etc)
     *                         'ipn_callback_url'		=> (string) Optional. URL to receive callbacks, should contain "http" or "https", eg. "https://nowpayments.io"
     *                         'order_id'				=> (string) Optional. Inner store order ID, e.g. "RGDBP-21314"
     *                         'order_description'		=> (string) Optional. Inner store order description, e.g. "Apple Macbook Pro 2019 x 1"
     *                         'success_url'			=> (string) Optional. URL where the customer will be redirected after successful payment
     *                         'cancel_url'				=> (string) Optional. URL where the customer will be redirected after failed payment
     *                         ]
     */
    public function createInvoice(array $params)
    {
        return $this->Call('POST', 'invoice', $params);
    }

    public function __destruct()
    {
        curl_close($this->session);
    }

    // withdraw


    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'address'				=> (string) Required. The address to validate.
     *                         'currency'				=> (string) Required. The currency of the address (e.g., btc, eth).
     *                         ]
     */
    public function validatePayoutAddress(array $params)
    {
        return $this->Call('POST', 'payout/validate-address', $params);
    }

    // create payout

    /**
     * @param  array  $params  Array of options
     *                         $params = [
     *                         'amount'				=> (int|float) Required. The amount to withdraw.
     *                         'currency'				=> (string) Required. The currency of the payout (e.g., btc, eth).
     *                         'address'				=> (string) Required. The address to send the payout to.
     *                         'extra_id'				=> (string) Optional. Extra ID or memo for the payout.
     *                        'ipn_callback_url'		=> (string) Optional. URL to receive callbacks, should contain "http" or "https", e.g., "https://nowpayments.io"
     *                         ]
     */
    public function createPayout(array $params)
    {

        if (empty($params['withdrawals'][0]['amount']) || empty($params['withdrawals'][0]['currency']) || empty($params['withdrawals'][0]['address'])) {
            throw new InvalidArgumentException('Required parameters are missing.');
        }
        if (!isset($params['account_email']) || !isset($params['account_password'])) {
            throw new InvalidArgumentException('Account email and password are required for payout.');
        }
        if (!isset($params['ipn_callback_url'])) {
            $params['ipn_callback_url'] = route('ipn.nowpayments');
        }

        $token = $this->getToken($params['account_email'], $params['account_password']);
        if (!$token['token']) {
            throw new InvalidArgumentException('Authentication failed: ' . $token['message']);
        }
        $params['token'] = $token['token'];

        unset($params['account_email'], $params['account_password']);


        return $this->Call('POST', 'payout', $params);
    }

    public function getToken($email, $password){
        return $this->Call('POST','auth',[
            'email' => $email,
            'password' => $password,
        ]);
    }
}
