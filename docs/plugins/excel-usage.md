# Excel Import/Export Usage Guide

This application includes two complementary packages for Excel functionality:

- **pxlrbt/filament-excel** - For exporting data from Filament tables
- **eightynine/filament-excel-import** - For importing Excel files into the database

## Export Functionality

### Table Export Action

Add export functionality to any Filament table:

```php
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->headerActions([
            ExportAction::make()
                ->exports([
                    ExcelExport::make('table')
                        ->fromTable()
                        ->withFilename('export-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                ]),
        ])
        ->bulkActions([
            ExportBulkAction::make()
                ->exports([
                    ExcelExport::make('selection')
                        ->fromTable()
                        ->except(['id'])
                        ->withFilename('selected-' . date('Y-m-d')),
                ]),
        ]);
}
```

### Customizing Columns

```php
use pxlrbt\FilamentExcel\Columns\Column;

ExcelExport::make()
    ->withColumns([
        Column::make('name')->heading('Full Name'),
        Column::make('email'),
        Column::make('created_at')
            ->heading('Registration Date')
            ->formatStateUsing(fn ($state) => $state->format('d/m/Y')),
    ])
```

## Import Functionality

### Adding Import Action to Table

```php
use EightyNine\ExcelImport\ExcelImportAction;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->headerActions([
            ExcelImportAction::make()
                ->color('primary')
                ->validateUsing([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                ])
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    foreach ($collection as $row) {
                        $modelClass::create($row);
                    }
                }),
        ]);
}
```

### Import with Custom Processing

```php
ExcelImportAction::make()
    ->slideOver()
    ->color('primary')
    ->processCollectionUsing(function (string $modelClass, Collection $collection) {
        $collection->each(function ($row) use ($modelClass) {
            $modelClass::updateOrCreate(
                ['email' => $row['email']],
                $row->except('email')->toArray()
            );
        });
    })
```

## Configuration

### Excel Import Configuration

The configuration file is at `config/excel-import.php`:

```php
return [
    'accepted_mimes' => [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
    ],
    'disk' => 'local',
    'directory' => 'excel-imports',
];
```

## Best Practices

1. **Always validate imported data** - Use the `validateUsing()` method
2. **Chunk large imports** - Process in batches to avoid memory issues
3. **Handle duplicates** - Use `updateOrCreate()` when appropriate
4. **Provide feedback** - Show progress for large operations
5. **Test with sample files** - Validate import logic before production use

## Related Files

- `config/excel-import.php` - Import configuration
- `database/migrations/*create_excel_import_table.php` - Import tracking table
