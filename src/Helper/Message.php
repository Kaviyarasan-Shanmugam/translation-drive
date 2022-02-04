<?php
namespace ProcessDrive\TranslationDrive\Helper;

class Message
{

    public function key($key)
    {
        $message = array(
            'env_required' => 'Please Check The ENV Configurations..!',
            'file_not_found' => 'The Excel Sheet Is Not Found..!',
            'import_success' => 'Excel Sheet To JS File Converted Successfully!',
            'null_excel_data' => 'The Excel Sheet Data Is Empty..!',
            'convert_json' => 'Could Not be Convert As A JSON : ',
            'export_success' => 'JS File To Excel Sheet Converted Successfully..!',
            'file_download_issue' => 'File download issue'
        );
        
        return @$message[$key] ? $message[$key] : null;
    }

    
}
