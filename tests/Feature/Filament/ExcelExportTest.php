<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_export_action_class_exists(): void
    {
        // pxlrbt/filament-excel provides export functionality
        $this->assertTrue(class_exists(\pxlrbt\FilamentExcel\Actions\Tables\ExportAction::class));
    }

    public function test_excel_export_bulk_action_class_exists(): void
    {
        $this->assertTrue(class_exists(\pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::class));
    }

    public function test_excel_column_class_exists(): void
    {
        $this->assertTrue(class_exists(\pxlrbt\FilamentExcel\Columns\Column::class));
    }
}
