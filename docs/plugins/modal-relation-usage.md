# Modal Relation Manager Usage Guide

The `guava/filament-modal-relation-managers` package allows embedding relation managers inside modal dialogs instead of inline on the page.

## Installation

The package is already installed and auto-registered with Filament.

## Basic Usage

### In Resource Edit Page

```php
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

class EditPost extends EditRecord
{
    use CanBeEmbeddedInModals;

    protected static string $resource = PostResource::class;

    public function getRelationManagers(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }
}
```

### Making Relation Manager Modal-Ready

```php
use Filament\Resources\RelationManagers\RelationManager;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

class CommentsRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'comments';
}
```

## Triggering Modal from Action

```php
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;

public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // ... other entries
        ])
        ->actions([
            RelationManagerAction::make('comments')
                ->label('View Comments')
                ->relationManager(CommentsRelationManager::class),
        ]);
}
```

## From Table Row Actions

```php
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->actions([
            RelationManagerAction::make('view_items')
                ->relationManager(OrderItemsRelationManager::class),
        ]);
}
```

## Use Cases

- Viewing related records without leaving the current page
- Quick editing of child records
- Preview of nested data
- Reducing page navigation
- Better UX for complex relationships

## Benefits

1. **Improved UX** - Users stay on the current page
2. **Faster workflows** - No page reloads needed
3. **Context preservation** - Users don't lose their place
4. **Clean interface** - Reduces page clutter

## Related Files

No configuration files - works via traits and actions.
