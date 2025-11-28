# Layout Manager Usage Guide

The `asosick/filament-layout-manager` package allows users to create and customize dashboard layouts.

## Installation

The package is already installed. It auto-registers with Filament panels.

## Features

- Drag-and-drop widget arrangement
- Multiple layout presets
- User-specific layout preferences
- Widget resizing capabilities
- Grid-based layout system

## Configuration

### In Panel Provider

```php
use Asosick\FilamentLayoutManager\FilamentLayoutManagerPlugin;

$panel->plugins([
    FilamentLayoutManagerPlugin::make()
        ->enableLayouts()
        ->defaultColumns(3)
        ->persistLayoutsInDatabase(),
]);
```

## Creating Layoutable Widgets

```php
use Filament\Widgets\Widget;
use Asosick\FilamentLayoutManager\Concerns\CanBeLayouted;

class StatsWidget extends Widget
{
    use CanBeLayouted;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;
}
```

## User Layout Persistence

The plugin can save user layout preferences:

```php
FilamentLayoutManagerPlugin::make()
    ->persistLayoutsInDatabase()
    ->layoutTable('user_layouts')
```

## Layout Presets

Define preset layouts:

```php
FilamentLayoutManagerPlugin::make()
    ->presets([
        'default' => [
            'columns' => 3,
            'widgets' => [
                'stats' => ['col' => 1, 'row' => 1, 'colspan' => 3],
                'chart' => ['col' => 1, 'row' => 2, 'colspan' => 2],
            ],
        ],
        'compact' => [
            'columns' => 2,
            'widgets' => [
                'stats' => ['col' => 1, 'row' => 1, 'colspan' => 2],
            ],
        ],
    ])
```

## Use Cases

- Personalized dashboards
- Role-based default layouts
- Different layouts for different contexts
- Widget organization flexibility

## Best Practices

1. Define sensible default layouts
2. Group related widgets logically
3. Consider mobile responsiveness
4. Test with various widget combinations
5. Document available layout options for users

## Related Files

No configuration files by default - configured in Panel Provider.
