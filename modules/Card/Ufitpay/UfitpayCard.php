<?php

namespace Card\Ufitpay;

use App\Models\CardHolder;
use Illuminate\Support\Facades\Http;

class UfitpayCard
{
    public function execute($card_holder_id)
    {
        // Create card in Ufitpay
        $user = auth('web')->user();
        $ufitpay = $this->client()->post('/create_virtual_card', [
            'card_brand' => 'mastercard',
            'card_currency' => 'USD',
            'card_holder_id' => $card_holder_id,
        ]);

        $card = $ufitpay->json();

        $response = (object) [
            'card_id' => $card['data']['id'],
            'currency' => $card['data']['currency'],
            'type' => $card['data']['type'],
            'status' => $card['data']['status'],
            'amount' => $card['data']['amount'],
            'expiration_month' => $card['data']['expiry_month'],
            'expiration_year' => $card['data']['expiry_year'],
            'last_four_digits' => substr($card['data']['card_number'], -4),
        ];

        return [
            'card' => $response,
            'data' => [
                'status' => $response->status,
                'amount' => $response->data->amount,
            ],
        ];

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
                'provider' => 'ufitpay',
                'type' => 'individual',
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
            ];

            // Create card holder in stripe

            $cardHolderResponse = $this->client()->post(
                'create_card_holder',
                [
                    'first_name' => str($request->name)->before(' '),
                    'last_name' => str($request->name)->after(' '),
                    'email' => $request->email,
                    'phone' => $request->phone_number,
                    'address' => $request->address,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'kyc_method' => $request->kyc_method,
                    'bvn' => $request->bvn,
                    'selfie_image' => $request->selfie_image,
                    'id_image' => $request->id_image,
                    'back_id_image' => $request->back_id_image,
                    'id_number' => $request->id_number,
                ]
            )->json('data');

            // Create card holder in database
            $card_holder = CardHolder::create([
                'user_id' => auth()->id(),
                'card_holder_id' => $cardHolderResponse->card_holder_id,
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

    public function updateCardStatus($card)
    {
        $stripe_card = $this->client()->post('/update_card_status', [
            'id' => $card->card_id,
            'status' => $card->status == 'active' ? 'inactive' : 'active',
        ])->json('data');

        // Update card status in database
        $card->update([
            'status' => $stripe_card->new_status,
        ]);

        return $card;
    }

    public function addCardBalance($card, $amount)
    {
        $this->client()->post('/fund_virtual_card', [
            'id' => $card->card_id,
            'amount' => $amount,
            'funding_currency' => 'USD',
        ]);

        // Update card balance in database
        $card->update(['amount' => $amount]);

        return $card;
    }

    public function withdrawCardBalance($card, $amount)
    {
        $this->client()->post('/withdraw_virtual_card_balance', [
            'id' => $card->card_id,
            'amount' => $amount,
            'withdrawal_currency' => 'USD',
        ]);

        // Update card balance in database
        $card->update(['amount' => $amount]);

        return $card;
    }

    public function validationRules($request)
    {
        $validator_rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'required|numeric|min:10',
            'address' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'kyc_method' => 'required|string|in:SELFIE_IMAGE,NIGERIAN_NIN,NIGERIAN_INTERNATIONAL_PASSPORT,NIGERIAN_PVC,KENYAN_PASSPORT,KENYAN_NATIONAL_ID,INDIA_NATIONAL_ID,TOGO_NATIONAL_ID,KENYAN_NATIONAL_ID,CAMEROON_NATIONAL_ID,SIERRA_LEONE_NATIONAL_ID,SIERRA_LEONE_PASSPORT,SENEGAL_ECOWAS_ID,INDIA_PASSPORT,BRAZIL_PASSPORT,UNITED_STATES_INTERNATIONAL_PASSPORT,UNITED_STATES_RESIDENCE_CARD,UNITED_KINGDOM_INTERNATIONAL_PASSPORT,ALGERIA_PASSPORT,GHANIAN_INTERNATIONAL_PASSPORT,IVORYCOAST_PASSPORT,SOUTHAFRICAN_PASSPORT,MOROCCO_PASSPORT,NETHERLAND_PASSPORT',
            'bvn' => 'required_if:kyc_method,NIGERIAN_DRIVERS_LICENSE,NIGERIAN_PVC,NIGERIAN_INTERNATIONAL_PASSPORT,NIGERIAN_NIN,SELFIE_IMAGE|string|max:11',
            'selfie_image' => 'required_if:kyc_method,SELFIE_IMAGE|url|mimes:jpg,jpeg,png',
            'id_image' => 'required_if:kyc_method,!=SELFIE_IMAGE|url|mimes:jpg,jpeg,png',
            'back_id_image' => 'required_if:kyc_method,KENYAN_NATIONAL_ID,UNITED_STATES_RESIDENCE_CARD,INDIA_NATIONAL_ID,TOGO_NATIONAL_ID,KENYAN_NATIONAL_ID,CAMEROON_NATIONAL_ID,SIERRA_LEONE_NATIONAL_ID,SENEGAL_ECOWAS_ID|url|mimes:jpg,jpeg,png',
            'id_number' => 'required|string|max:255',
        ];

        return $validator_rules;
    }

    public function getCardDetails($card_id)
    {
        return $this->client()->post('/get_virtual_card', [
            'id' => $card_id,
        ])->json('data');
    }

    public function getCardTransactions($card_id)
    {
        return $this->client()->post('/get_card_transactions', [
            'id' => $card_id,
        ])->json('data');
    }

    protected function client()
    {
        $ufitpayCredential = plugin_active('Ufitpay Virtual Card');
        $ufitpay_api_key = $ufitpayCredential ? json_decode($ufitpayCredential->data, true)['api_key'] : null;
        $ufitpay_api_token = $ufitpayCredential ? json_decode($ufitpayCredential->data, true)['api_token'] : null;
        $ufitpay = Http::acceptJson()
            ->withHeaders([
                'Api-Key' => $ufitpay_api_key,
                'Api-Token' => $ufitpay_api_token,
            ])
            ->baseUrl('https://api.ufitpay.com/v1');

        return $ufitpay;
    }
}
