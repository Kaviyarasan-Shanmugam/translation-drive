<?php

namespace ProcessDrive\TranslationDrive\Tests;

use PHPUnit\Framework\TestCase;
use ProcessDrive\TranslationDrive\Commands\ExcelToJS;
use ProcessDrive\TranslationDrive\Commands\JSToExcel;
use ProcessDrive\TranslationDrive\Helper\Validation;
use ProcessDrive\TranslationDrive\Import\ImportData;
use ProcessDrive\TranslationDrive\Export\ExportData;


class PackageTest extends TestCase
{
    use Validation, ImportData, ExportData;
    
    /** @test */
    public function it_has_reload_cache_command()
    { 
        $this->assertTrue(class_exists(ExcelToJS::class));
        $this->assertTrue(class_exists(JSToExcel::class));
    }

    
}
