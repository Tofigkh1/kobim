<?php
// Widgetler duzgun sekilde yazilmali ve goto dan bir dene var iki funksiya olmali biri user ucun digeri application ucun 
namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApplicationWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $applicationCount = Application::whereDoesntHave('employeeTasks')->count();
        $userCount = User::count();

        if (auth()->user()->isAdmin()) {
            $stat = Stat::make("Müraciətlər", $applicationCount)
                ->description($applicationCount > 0 ? "Yeni daxil olan müraciət" : "Yeni müraciət yoxdur")
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis', IconPosition::Before)
                ->chart([1, 2, 3, 4, 5])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'goto()',
                ])
                ->color($applicationCount > 0 ? "success" : "warning");
            
                
            $user = Stat::make("Əməkdaşlar", $userCount)
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->description("Sistemdə olan əməkdaşlar")
                ->chart([1, 2, 3, 4, 5])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'goto()',
                ])
                ->color($userCount > 0 ? "info" : "warning");

                    
            return [$stat,$user];

        }


        return [];
    }

    public function goto()
    {
        return redirect()->to('/admin/applications?activeTab=Yönləndirilməmiş+müraciətlər');
    }
}
