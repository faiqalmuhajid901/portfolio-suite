<?php

namespace App\Http\Controllers;

use App\Models\PortfolioVisit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnalyticsHeartbeatController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $visitorId = $request->cookie('portfolio_visitor_id');

        if (blank($visitorId)) {
            return response()->noContent();
        }

        $latestVisit = PortfolioVisit::query()
            ->where('visitor_id', $visitorId)
            ->latest('last_seen_at')
            ->first();

        if ($latestVisit) {
            $latestVisit->forceFill([
                'last_seen_at' => now(),
            ])->saveQuietly();
        }

        return response()->noContent();
    }
}
