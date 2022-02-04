<?php

namespace ProcessDrive\TranslationDrive\Helper;

use ProcessDrive\TranslationDrive\Helper\Message;


trait Validation
{

    public function validateENVData()
    {
        $env_locale_language    =   env('JS_LOCALES') ? explode( "," ,env('JS_LOCALES')) : [];
        $env_key_type           =   env('JS_KEY_STRING');
        $env_directory          =   env('DIRECTORY') ? (env('DIRECTORY') == "resources") ? resource_path() : public_path() : '';
        $env_project_directory  =   env('JS_PROJECT_PATH');
        $env_find_directory     =   env('FIND_DIRECTORY') ? explode(',', env('FIND_DIRECTORY')) : [];
        
        $scan_directory         =   $this->scanDirectory($env_directory.'/'.$env_project_directory);
        if ($env_locale_language && $env_key_type && $env_directory && $env_project_directory && $env_find_directory && $scan_directory) {
            return [
                'env_locale_language' => $env_locale_language, 'env_key_type' => ($env_key_type == "no") ? false : true,
                'env_directory' => $env_directory, 'env_project_directory' => $env_project_directory, 'env_find_directory' => $env_find_directory
            ];
        } else {
            $this->error(Message::key('env_required'));
        }
    }

    public function scanDirectory($path)
    {
        try {
            scandir($path);
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }
}
