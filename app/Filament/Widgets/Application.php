<?php

namespace App\Filament\Widgets;

use App\Models\Application as ApplicationModel;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class Application extends ChartWidget
{
    protected static ?string $heading = 'Müraciətlər';
    protected static string $color = 'info';


    protected function getData(): array
    {
        $data = Trend::model(ApplicationModel::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Daxil olan müraciətlər',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
