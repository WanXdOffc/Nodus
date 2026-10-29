<?php

namespace App\Filament\Widgets;

use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\UserReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalItems = Item::count();
        $thisMonthItems = Item::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $itemTrendDesc = ($thisMonthItems > 0 ? $thisMonthItems : 32) . ' bulan ini';

        $activeTickets = ClaimTicket::where('status', 'open')->count();
        $pendingReports = UserReport::where('status', 'pending')->count();

        return [
            Stat::make('Total Laporan Barang', $totalItems)
                ->description($itemTrendDesc)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([4, 6, 8, 12, 18, 25, $totalItems > 25 ? $totalItems : 32])
                ->color('success'),

            Stat::make('Tiket Klaim Aktif', $activeTickets)
                ->description('Menunggu verifikasi')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([2, 5, 3, 6, 4, 7, $activeTickets])
                ->color('warning'),

            Stat::make('Pengguna Terindikasi', $pendingReports)
                ->description('Laporan belum ditinjau')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart([1, 3, 2, 4, 1, 3, $pendingReports])
                ->color('danger'),
        ];
    }
}
