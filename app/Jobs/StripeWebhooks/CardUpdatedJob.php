<?php

namespace App\Jobs\StripeWebhooks;

use App\Models\Card;
use App\Services\CreateStripeInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class CardUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('=====================Start card update job=====================');

        // Handle the incoming webhook
        $data = $this->webhookCall->payload['data']['object'];
        $card_id = $data['id'];

        info('Card id:'.$card_id);

        // Card data
        $card = Card::where('card_id', $card_id)->first();

        // Update card balance
        if ($card) {
            $stripe = (new CreateStripeInstance)->execute();

            $issuingCard = $stripe->issuing->cards->retrieve($card_id);
            $balance = $issuingCard->spending_controls->spending_limits[0]->amount;

            $card->update([
                'amount' => $balance == 1 ? 0 : $balance,
            ]);
        }
        info('=====================End card update job=====================');
    }
}
