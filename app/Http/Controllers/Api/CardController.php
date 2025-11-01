<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Services\CardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardController extends Controller
{
    public function __construct(
        private CardService $cardService
    ) {}

    public function index()
    {
        $cards = Card::currentUser()->with('cardHolder')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $cards,
        ]);
    }

    public function store(Request $request)
    {
        try {

            $this->cardService->validate($request);

            $this->cardService->process($request);

            return response()->json([
                'status' => true,
                'message' => __('Card created successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id)
    {
        $card = Card::with('cardHolder')->currentUser()->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $card,
        ]);
    }

    public function updateStatus($card_id)
    {
        try {
            $card = Card::currentUser()->findOrFail($card_id);

            $this->cardService->updateStatus($card);

            return response()->json([
                'status' => true,
                'message' => __('Card status updated successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function topupBalance(Request $request, $id)
    {
        try {
            $card = Card::currentUser()->findOrFail($id);

            $this->cardService->addCardBalance($card, $request);

            return response()->json([
                'status' => true,
                'message' => __('Card balance added successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function transactions(Request $request, $card_id)
    {
        // Check cache first
        $cacheKey = 'card_transactions_'.$card_id;

        // Forget cache if sync is requested
        if ($request->boolean('sync') && Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }

        if (Cache::has($cacheKey)) {
            $transactions = Cache::get($cacheKey);
        } else {
            // Get transactions from service
            $transactions = $this->cardService->transactions($card_id);

            // Cache transactions for 1 hour if they exist
            if ($transactions && count($transactions)) {
                Cache::put($cacheKey, $transactions, now()->addHour());
            }
        }

        return response()->json([
            'status' => true,
            'data' => $transactions,
        ]);
    }

    public function cardholders()
    {
        $cardholders = CardHolder::currentUser()->get();

        return response()->json([
            'status' => true,
            'data' => $cardholders,
        ]);
    }
}
