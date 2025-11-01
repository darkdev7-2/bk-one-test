<?php

namespace App\Jobs;

use App\Models\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class CardActivate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $card_id
    ) {}

    public function handle(): void
    {
        // Card data
        $card = Card::where('card_id', $this->card_id)->first();

        if (! $card) {
            return;
        }

        $stripeCredential = plugin_active('Stripe Virtual Card');
        $stripe_secret = $stripeCredential ? json_decode($stripeCredential->data, true)['secret_key'] : null;

        $stripe = new StripeClient($stripe_secret);

        $issuingCard = $stripe->issuing->cards->retrieve($this->card_id, [
            'expand' => [
                'number',
                'cvc',
            ],
        ]);

        $balance = $issuingCard->spending_controls->spending_limits[0]->amount;

        $card->update([
            'amount' => $balance,
            'card_number' => $issuingCard->number,
            'cvc' => $issuingCard->cvc,
        ]);

    }
}
