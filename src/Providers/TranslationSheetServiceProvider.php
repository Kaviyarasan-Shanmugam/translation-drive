<?php

namespace ProcessDrive\TranslationDrive\Providers;

use Illuminate\Support\ServiceProvider;
use ProcessDrive\TranslationDrive\Commands\ExcelToJS;
use ProcessDrive\TranslationDrive\Commands\JSToExcel;

class TranslationSheetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ExcelToJS::class,
            JSToExcel::class
        ]);
    }

}