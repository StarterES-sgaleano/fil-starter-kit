<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SampleChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'sampleChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Sample Chart';

    /**
     * Chart options (see https://apexcharts.com/docs/options)
     *
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Sample Data',
                    'data' => [30, 40, 35, 50, 49, 60, 70, 91, 125],
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }
}
