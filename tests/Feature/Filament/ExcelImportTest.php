<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
      use RefreshDatabase;

      public function test_excel_import_action_class_exists(): void
      {
            // eightynine/filament-excel-import provides import functionality
            $this->assertTrue(class_exists(\EightyNine\ExcelImport\ExcelImportAction::class));
      }

      public function test_excel_import_table_exists(): void
      {
            // Verify the filament_excel_import_table was created
            $this->assertTrue(
                  \Illuminate\Support\Facades\Schema::hasTable('filament_excel_import_table')
            );
      }
}
