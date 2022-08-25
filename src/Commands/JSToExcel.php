<?php

namespace ProcessDrive\TranslationDrive\Commands;

use ProcessDrive\TranslationDrive\Helper\Validation;
use ProcessDrive\TranslationDrive\Helper\Message;
use ProcessDrive\TranslationDrive\Export\ExportData;
use Illuminate\Console\Command;

class JSToExcel extends Command
{
    use Validation, ExportData;

    protected $signature = 'translation:export';

    protected $description = 'Local language file data convert to the excel sheet';

    public function handle()
    {
        $get_env_data   =   $this->validateENVData();

        if ($get_env_data) {
            $this->convertJSToExcel($get_env_data);
        }
    }
}
