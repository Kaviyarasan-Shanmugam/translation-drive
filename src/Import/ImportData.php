<?php
namespace ProcessDrive\TranslationDrive\Import;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ProcessDrive\TranslationDrive\Helper\Message;

trait ImportData
{

    public function convertExcelToJS ($env_data)
    {
        $get_excel_data     =   $this->scanExcelSheet($env_data['env_directory'], $env_data['env_project_directory']);
        if (is_array($get_excel_data)) {
            $excel_header               =   $get_excel_data[0];
            unset($get_excel_data[0]);
            $associative_excel_data     =   $this->indexedToAssociative($get_excel_data, $excel_header);
            $group_by_path              =   collect($associative_excel_data)->groupBy('PATH')->toArray();
            $result                     =   $this->groupByResult($group_by_path, array_intersect($excel_header, $env_data['env_locale_language']));
            $completeContent            =   $this->createContent($result, $env_data['env_key_type']);
            foreach ($completeContent as $folderPath => $wContent) {
                $this->writeTranslationInJs($folderPath, $wContent);
            }
            $this->info(Message::key('import_success'));
        }
    }

    /**
     * Scan the excel file and convert to PHP Array
     */
    public function scanExcelSheet ($directory, $project_directory)
    {
        $full_path          =   $directory.'/'.$project_directory;
        $read_excel_data    =   array();
        if ($xlsx = \Shuchkin\SimpleXLSX::parse($full_path.'/'.$project_directory.'translationFile.Xlsx')) {
            foreach ($xlsx->rows() as $r) {
                $read_excel_data[] = $r;
            }
            return count($read_excel_data) > 1 ? $read_excel_data : $this->info(Message::key('null_excel_data'));
        } else {
            $this->info(Message::key('file_not_found'));
        }

    }

    /**
     * Change the indexed array to associative array based on excel header
     */
    public function indexedToAssociative ($get_excel_data, $excel_header)
    {
        foreach ($get_excel_data as $value) {
            $read_excel  = array();
            foreach ($value as $key => $value) {
                $read_excel[$excel_header[$key]] = $value;
            }
            $convertNameKeyArray[] = $read_excel;
        }
        return $convertNameKeyArray;
    }

    /**
     * Make result
     */
    public function groupByResult ($group_by_path, $locales) {
        $groupByResult     =   array();
        foreach ($group_by_path as $path => $allLocaleFileData) {
            foreach ($allLocaleFileData as $singleArray) {
                foreach ($locales as $localeName) {
                    $explodeKey     = explode('.', $singleArray['KEY']);
                    $explodeKey[0]  = $explodeKey[0] == 'locale' ? $localeName : $explodeKey[0];
                    array_unshift($explodeKey,$localeName);
                    array_unshift($explodeKey,$path);
                    $this->assignArrayByPath($groupByResult, implode('.', $explodeKey), $singleArray[$localeName]);
                }
            }
        }
        return $groupByResult;   
    }

    /**
     * Split key and make array
     */
    public function assignArrayByPath(&$resultData, $path, $value, $separator='.') {
        $keys = explode($separator, $path);
        foreach ($keys as $key) {
            $resultData = &$resultData[$key];
        }
        $resultData = $value;
    }

    /**
     * Write JS File Content
     */
    public function createContent ($groupByResult, $keyType) {
        $completeContent   = array();
        foreach ($groupByResult as $getPath => $languageData) {
            foreach ($languageData as $fileLocaleName => $contentData) {
                foreach ($contentData as $moduleNameValue => $moduleData) { 
                    $content = $keyType ? "export const {$moduleNameValue} = {" : "const {$moduleNameValue} = {";
                    $this->writeContent($moduleData, "\n\t", $keyType, $content);
                    if ($keyType) {
                        $content .= "\n}";
                    } else {
                        $content .= "\n};";
                        $content .= "\nexport default {$moduleNameValue};";
                    }
                    $completeContent[$getPath.$fileLocaleName.'.js'] = $content;
                } 
            }
        }
        return $completeContent;
    }

    /**
     * Write the JS File As like JS Format
     */
    public function writeContent ($moduleData, $newline, $keyType, &$content) {
        $i = 1;
        $commaCount = count($moduleData);
        foreach ($moduleData as $key => $value) {
            if (is_array($value)) {
                $key = $keyType ? "\"{$key}\"" : $key;
                $content .= "{$newline}{$key}: {";
                $this->writeContent($value, $newline."\t", $keyType, $content);
                $addComma = $commaCount == $i ? "" : ",";
                $content .= "{$newline}}{$addComma}";
            } else {
                $key = $keyType ? "\"{$key}\"" : $key;
                $addComma = $commaCount == $i ? "" : ",";
                $content .= "{$newline}{$key}: \"{$value}\"{$addComma}";
            }
            $i++;
        }
        return $content;
    }

    /**
     * Make JS file 
     */
    public function writeTranslationInJs($path, $content) {
        // Replace locale js file
        $folder = explode('/', $path);
        array_pop($folder);
        $folder = "./".env('DIRECTORY')."/".implode('/', $folder);
        $file = "./".env('DIRECTORY')."/".$path;
        // make directory
        $folders = explode("/", $folder);
        $fpath   = "";
        foreach ($folders as $dir) {
            $fpath .= $dir . '/';
            if (!is_dir($fpath)) {
                mkdir($fpath, 0775);
            }

            chmod($fpath, 0775);
        }
        // Check if locale js file exists - Recreate if not
        if (!file_exists($file)) {
            $fh = fopen($file, 'w');
        }
        // Write the new localisation to locale js file
        file_put_contents($file, $content);
    }
}
