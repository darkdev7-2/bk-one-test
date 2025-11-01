<?php

namespace Card\Flutterwave;

use Illuminate\Support\Facades\Http;

class FlutterwaveCard
{
    public function execute()
    {
        // Create card in Flutterwave
        $user = auth('web')->user();
        $flutterwave = $this->client()->post('/new', [
            'currency' => 'usd',
            'amount' => 0,
            'billing_name' => $user->full_name,
            // 'first_name' => $user->first_name,
            // 'last_name' => $user->last_name,
            // 'gender' => $user->gender,
            // 'title' => $user->gender == 'female' ? 'Mrs' : 'Mr',
            // 'date_of_birth' => $user->date_of_birth,
            // 'email' => $user->email,
            // 'phone' => $user->phone,

            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
            'dateofbirth' => $user->date_of_birth,
            'email' => $user->email,
            'phone' => $user->phone,
            'title' => $user->gender == 'female' ? 'Mrs' : 'Mr',
            'gender' => $user->gender,

        ]);

        $card = $flutterwave->json();

        $response = (object) [
            'card_id' => $card['data']['id'],
            'currency' => $card['data']['currency'],
            'type' => $card['data']['type'],
            'status' => $card['data']['status'],
            'amount' => $card['data']['amount'],
            'expiration_month' => $card['data']['expiration_month'],
            'expiration_year' => $card['data']['expiration_year'],
            'last_four_digits' => $card['data']['last4'],
        ];

        return [
            'card' => $response,
            'data' => [
                'status' => $response->status,
                'amount' => $response->data->amount,
            ],
        ];

    }

    public function getCardHolder($cardholder_id)
    {
        return null;

    }

    public function updateCardStatus($card)
    {
        $stripe_card = $this->client()->issuing->cards->update($card->card_id, [
            'status' => $card->status == 'active' ? 'inactive' : 'active',
        ]);

        // Update card status in database
        $card->update([
            'status' => $stripe_card->status,
        ]);

        return $card;
    }

    public function addCardBalance($card, $amount)
    {
        $this->client()->issuing->cards->update($card->card_id, [
            'spending_controls' => [
                'spending_limits' => [
                    [
                        'amount' => $amount, // The spending limit in cents (e.g., $50.00)
                        'interval' => 'all_time', // The interval for the limit (e.g., daily, weekly, monthly, yearly, all_time)
                    ],
                ],
            ],
        ]);

        // Update card balance in database
        $card->update(['amount' => $amount]);

        return $card;
    }

    public function validationRules($request)
    {
        $validator_rules = [
            'date_of_birth' => 'required|date',
        ];

        return $validator_rules;
    }

    public function getCardDetails($card_id)
    {
        return $this->client()->issuing->cards->retrieve($card_id);
    }

    public function getCardTransactions($card_id)
    {
        return $this->client()->issuing->transactions->all([
            'card' => $card_id,
            'limit' => 5,
        ]);
    }

    protected function client()
    {
        $flutterwaveCredential = plugin_active('Flutterwave Virtual Card');
        $flutterwave_secret = $flutterwaveCredential ? json_decode($flutterwaveCredential->data, true)['secret_key'] : null;
        $body['secret_key'] = $flutterwave_secret;
        $flutterwave = Http::acceptJson()
        // ->withToken($flutterwave_secret)
            ->baseUrl('https://api.ravepay.co/v2/services/virtualcards');

        return $flutterwave;
    }
}
