<?php

namespace App\Services;

use App\Models\PortfolioVisit;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class PortfolioAnalyticsService
{
    public function dashboard(int $trendDays = 14): array
    {
        $now = CarbonImmutable::now();
        $todayStart = $now->startOfDay();
        $tomorrowStart = $todayStart->addDay();
        $sevenDaysStart = $todayStart->subDays(6);
        $previousSevenDaysStart = $sevenDaysStart->subDays(7);
        $thirtyDaysStart = $todayStart->subDays(29);
        $onlineSince = $now->subMinutes(2);

        $currentSevenDayViews = PortfolioVisit::query()
            ->where('created_at', '>=', $sevenDaysStart)
            ->count();

        $previousSevenDayViews = PortfolioVisit::query()
            ->whereBetween('created_at', [$previousSevenDaysStart, $sevenDaysStart])
            ->count();

        $summary = [
            'online_now' => PortfolioVisit::query()
                ->where('last_seen_at', '>=', $onlineSince)
                ->distinct()
                ->count('visitor_id'),
            'views_today' => PortfolioVisit::query()
                ->whereBetween('created_at', [$todayStart, $tomorrowStart])
                ->count(),
            'visitors_today' => PortfolioVisit::query()
                ->whereBetween('created_at', [$todayStart, $tomorrowStart])
                ->distinct()
                ->count('visitor_id'),
            'views_7_days' => $currentSevenDayViews,
            'visitors_30_days' => PortfolioVisit::query()
                ->where('created_at', '>=', $thirtyDaysStart)
                ->distinct()
                ->count('visitor_id'),
            'growth_7_days' => $this->growthPercentage(
                current: $currentSevenDayViews,
                previous: $previousSevenDayViews,
            ),
        ];

        $trend = $this->trend($trendDays, $todayStart);
        $devices = $this->devices($thirtyDaysStart);
        $sources = $this->sources($thirtyDaysStart);

        return [
            'summary' => $summary,
            'trend' => $trend,
            'devices' => $devices,
            'sources' => $sources,
            'top_pages' => $this->topPages($thirtyDaysStart),
            'locations' => $this->locations($thirtyDaysStart),
            'realtime_visitors' => $this->realtimeVisitors($onlineSince),
            'chart_payload' => [
                'trend' => $trend,
                'devices' => $devices,
                'sources' => $sources,
            ],
        ];
    }

    private function trend(int $days, CarbonImmutable $todayStart): array
    {
        $days = max(7, min($days, 30));
        $firstDay = $todayStart->subDays($days - 1);

        $visits = PortfolioVisit::query()
            ->where('created_at', '>=', $firstDay)
            ->get(['visitor_id', 'created_at'])
            ->groupBy(fn (PortfolioVisit $visit): string => $visit->created_at->format('Y-m-d'));

        $labels = [];
        $views = [];
        $visitors = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $firstDay->addDays($offset);
            $key = $date->format('Y-m-d');
            /** @var Collection<int, PortfolioVisit> $dailyVisits */
            $dailyVisits = $visits->get($key, collect());

            $labels[] = $date->format('d M');
            $views[] = $dailyVisits->count();
            $visitors[] = $dailyVisits->pluck('visitor_id')->unique()->count();
        }

        return compact('labels', 'views', 'visitors');
    }

    private function topPages(CarbonImmutable $start): array
    {
        return PortfolioVisit::query()
            ->select('path')
            ->selectRaw('COUNT(*) AS views')
            ->selectRaw('COUNT(DISTINCT visitor_id) AS visitors')
            ->where('created_at', '>=', $start)
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(8)
            ->get()
            ->map(fn (PortfolioVisit $visit): array => [
                'path' => $visit->path,
                'views' => (int) $visit->getAttribute('views'),
                'visitors' => (int) $visit->getAttribute('visitors'),
            ])
            ->all();
    }

    private function devices(CarbonImmutable $start): array
    {
        $rows = PortfolioVisit::query()
            ->select('device_type')
            ->selectRaw('COUNT(*) AS total')
            ->where('created_at', '>=', $start)
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('device_type')->map(fn (string $value): string => ucfirst($value))->all(),
            'values' => $rows->map(fn (PortfolioVisit $visit): int => (int) $visit->getAttribute('total'))->all(),
        ];
    }

    private function sources(CarbonImmutable $start): array
    {
        $sources = PortfolioVisit::query()
            ->where('created_at', '>=', $start)
            ->pluck('referrer_host')
            ->map(fn (?string $host): string => $host ?: 'Direct')
            ->countBy()
            ->sortDesc()
            ->take(6);

        return [
            'labels' => $sources->keys()->values()->all(),
            'values' => $sources->values()->all(),
        ];
    }

    private function locations(CarbonImmutable $start): array
    {
        return PortfolioVisit::query()
            ->select(['country_code', 'city'])
            ->selectRaw('COUNT(*) AS views')
            ->where('created_at', '>=', $start)
            ->whereNotNull('country_code')
            ->groupBy(['country_code', 'city'])
            ->orderByDesc('views')
            ->limit(6)
            ->get()
            ->map(fn (PortfolioVisit $visit): array => [
                'location' => collect([$visit->city, $visit->country_code])
                    ->filter()
                    ->implode(', '),
                'views' => (int) $visit->getAttribute('views'),
            ])
            ->all();
    }

    private function realtimeVisitors(CarbonImmutable $onlineSince): array
    {
        return PortfolioVisit::query()
            ->where('last_seen_at', '>=', $onlineSince)
            ->latest('last_seen_at')
            ->limit(100)
            ->get()
            ->unique('visitor_id')
            ->take(10)
            ->map(fn (PortfolioVisit $visit): array => [
                'visitor' => 'Visitor '.substr($visit->visitor_id, 0, 8),
                'path' => $visit->path,
                'device' => ucfirst($visit->device_type),
                'browser' => $visit->browser ?: 'Unknown',
                'location' => collect([$visit->city, $visit->country_code])
                    ->filter()
                    ->implode(', ') ?: 'Unknown',
                'last_seen' => $visit->last_seen_at?->diffForHumans() ?: '-',
            ])
            ->values()
            ->all();
    }

    private function growthPercentage(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
