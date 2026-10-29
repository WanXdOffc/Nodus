<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ItemsChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pelaporan Barang (Lost vs Found)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $labels = [];
        $lostData = [];
        $foundData = [];

        // Hitung data aktual per bulan selama 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $targetDate = Carbon::now()->subMonths($i);
            $year = $targetDate->year;
            $month = $targetDate->month;

            $labels[] = $monthNames[$month] ?? $targetDate->format('M');

            $lostCount = Item::where('type', 'lost')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $foundCount = Item::where('type', 'found')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $lostData[] = $lostCount;
            $foundData[] = $foundCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Barang Kehilangan (Lost)',
                    'data' => $lostData,
                    'borderColor' => '#FF6B35',
                    'backgroundColor' => 'rgba(255, 107, 53, 0.12)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Barang Ditemukan (Found)',
                    'data' => $foundData,
                    'borderColor' => '#00C2A8',
                    'backgroundColor' => 'rgba(0, 194, 168, 0.12)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
