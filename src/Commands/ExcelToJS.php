<?php

namespace ProcessDrive\TranslationDrive\Commands;

use ProcessDrive\TranslationDrive\Helper\Validation;
use ProcessDrive\TranslationDrive\Helper\Message;
use ProcessDrive\TranslationDrive\Import\ImportData;


use Illuminate\Console\Command;

class ExcelToJS extends Command
{
    use Validation, ImportData;
    
    protected $signature = 'translation:import';

    protected $description = 'Excel sheet data convert to the local language file';

    public function handle()
    {
        
        $get_env_data   =   $this->validateENVData();
        
        if ($get_env_data) {
            $this->convertExcelToJS($get_env_data);
        }
    }

}
