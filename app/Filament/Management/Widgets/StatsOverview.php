<?php

namespace App\Filament\Management\Widgets;

use App\Models\StaffManagement;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s'; // Customize the refresh rate
    protected static bool $isLazy = false; // Disable lazy loading

    protected function getStats(): array
    {
        // Example: Total number of employees
        $totalUsers = User::count();

        $totalStaffs = StaffManagement::count();


        return [
            Stat::make('Total System Users', $totalUsers)
            ->description('Currently active system users')
                ->descriptionIcon('heroicon-m-users')
                ->color(color: 'success'),


            Stat::make('Total Hotel Staffs', $totalStaffs)
            ->description('Total number of Hotel Staffs')
            ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

        ];
    }
}
