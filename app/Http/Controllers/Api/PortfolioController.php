<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioResource;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    public function __invoke()
    {
        $portfolios = Portfolio::active()->get();

        return response()->json([
            'status' => true,
            'data' => PortfolioResource::collection($portfolios),
        ]);
    }
}
