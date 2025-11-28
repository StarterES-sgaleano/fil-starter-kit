# Badgeable Column Usage Guide

The `awcodes/filament-badgeable-column` package allows you to add badges to any Filament table column.

## Installation

The package is already installed. No additional configuration required.

## Basic Usage

```php
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            BadgeableColumn::make('title')
                ->badges([
                    Badge::make('status')
                        ->label(fn ($record) => $record->status->getLabel())
                        ->color(fn ($record) => $record->status->getColor()),
                ]),
        ]);
}
```

## Multiple Badges

```php
BadgeableColumn::make('name')
    ->badges([
        Badge::make('is_featured')
            ->label('Featured')
            ->color('success')
            ->visible(fn ($record) => $record->is_featured),
        Badge::make('is_published')
            ->label('Published')
            ->color('primary')
            ->visible(fn ($record) => $record->is_published),
    ])
```

## Badge Positioning

```php
BadgeableColumn::make('title')
    ->prefixBadges([
        Badge::make('priority')
            ->label(fn ($record) => $record->priority)
            ->color('warning'),
    ])
    ->suffixBadges([
        Badge::make('category')
            ->label(fn ($record) => $record->category->name),
    ])
```

## Conditional Badges

```php
Badge::make('overdue')
    ->label('Overdue')
    ->color('danger')
    ->visible(fn ($record) => $record->due_date < now())
```

## Use Cases

- Status indicators on resource listings
- Priority labels on tasks
- Category tags on content
- Feature flags visibility
- User role badges

## Related Files

This is a column component - no configuration files needed.
