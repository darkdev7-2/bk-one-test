<?php

namespace Card\Stripe;

use App\Models\CardHolder;
use Stripe\StripeClient;

class StripeCard
{
    public function execute($cardholder)
    {
        // Create card in stripe
        $stripe_card = $this->client()->issuing->cards->create([
            'cardholder' => $cardholder->card_holder_id,
            'currency' => 'usd',
            'type' => 'virtual',
            'spending_controls' => [
                'spending_limits' => [
                    [
                        'amount' => 1,
                        'interval' => 'all_time',
                    ],
                ],
            ],
        ]);

        $data = [
            'card_holder_id' => $cardholder->id,
            'currency' => 'usd',
            'type' => 'virtual',
            'status' => 'active',
            'amount' => 0,
            'expiration_month' => $stripe_card->exp_month,
            'expiration_year' => $stripe_card->exp_year,
            'last_four_digits' => $stripe_card->last4,
        ];

        return [
            'card' => $stripe_card,
            'data' => $data,
        ];
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
        try {
            $this->client()->issuing->cards->update($card->card_id, [
                'spending_controls' => [
                    'spending_limits' => [
                        [
                            'amount' => $card->amount + $amount, // The spending limit in cents (e.g., $50.00)
                            'interval' => 'all_time', // The interval for the limit (e.g., daily, weekly, monthly, yearly, all_time)
                        ],
                    ],
                ],
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }

        // Update card balance in database
        $card->update(['amount' => $card->amount + $amount]);

        return $card;
    }

    public function validationRules($request)
    {
        if ($request->type == 'existing_one') {
            $validator_rules = [
                'cardholder_id' => 'required|exists:card_holders,id',
            ];
        } else {
            $validator_rules = [
                'name' => 'required|string',
                'email' => 'nullable|email',
                'phone_number' => 'nullable|string',
                // 'type' => 'required|in:individual,business',
                'address' => 'required|string',
                'country' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'postal_code' => 'required|string',
            ];
        }

        return $validator_rules;
    }

    public function getCardHolder($request)
    {
        if ($request->type == 'existing_one') {
            // Take card holder data from existing card holder
            $card_holder = CardHolder::find($request->cardholder_id);
        } else {
            // Create card holder data
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'status' => $request->status,
                'provider' => 'stripe',
                'type' => 'individual',
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
            ];

            // Create card holder in stripe
            $stripe_card_holder = $this->client()->issuing->cardholders->create([
                'type' => $data['type'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'type' => 'individual',
                'individual' => [
                    'first_name' => auth()->user()->first_name,
                    'last_name' => auth()->user()->last_name,
                    'dob' => ['day' => 1, 'month' => 11, 'year' => 2000],
                    'card_issuing' => [
                        'user_terms_acceptance' => [
                            'date' => now()->getTimestamp(),
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ],
                    ],
                ],
                'billing' => [
                    'address' => [
                        'line1' => $data['address'],
                        'city' => $data['city'],
                        'state' => $data['state'],
                        'country' => $data['country'],
                        'postal_code' => $data['postal_code'],
                    ],
                ],
            ]);

            // Create card holder in database
            $card_holder = CardHolder::create([
                'user_id' => auth()->id(),
                'card_holder_id' => $stripe_card_holder->id,
                'provider' => 'stripe',
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'type' => $data['type'],
                'address' => $data['address'],
                'country' => $data['country'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['postal_code'],
            ]);
        }

        return $card_holder;
    }

    public function getCardDetails($card_id)
    {
        return $this->client()->issuing->cards->retrieve($card_id, [
            'expand' => [
                'number',
                'cvc',
            ],
        ]);
    }

    public function getCardTransactions($card_id)
    {
        try {
            $transactions = $this->client()->issuing->transactions->all([
                'card' => $card_id,
                'limit' => 5,
            ]);

            return array_map(function ($transaction) {
                return (object) [
                    'id' => $transaction->id,
                    'created' => now()->parse($transaction->created)->format('d M Y h:i A'),
                    'amount' => $transaction->amount,
                    'currency' => strtoupper($transaction->currency),
                    'status' => $transaction->status ?? 'Success',
                    'merchant_data' => (object) [
                        'name' => $transaction->merchant_data->name,
                    ],
                ];
            }, $transactions->data);
        } catch (\Throwable $th) {
            return [];
        }
    }

    protected function client()
    {
        $stripeCredential = plugin_active('Stripe Virtual Card');
        $stripe_secret = $stripeCredential ? json_decode($stripeCredential->data, true)['secret_key'] : null;

        $stripe = new StripeClient($stripe_secret);

        return $stripe;
    }
}
