<?php
namespace ProcessDrive\TranslationDrive\Export;

use PHPExcel;
use PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ProcessDrive\TranslationDrive\Helper\Message;

trait ExportData
{

    /**
     * Convert the Locales JS file to Excel Functionality
     */
    public function convertJSToExcel($env_data) {    
        $getAllLangJS   = $this->getAllLanguageFile($env_data['env_directory'].'/'.$env_data['env_project_directory'], $env_data['env_find_directory']);
        $getLangfile    = $this->filterFiletoLanguage($getAllLangJS, $env_data['env_locale_language']);
        $groupbyModule  = [];
        $results        = array();
        foreach ($getLangfile as $folderPath) {
            $getRealData    = file_get_contents($folderPath);
            $path           = substr($folderPath,stripos($folderPath,env('DIRECTORY').'/'), -5);
            $fileLocale     = explode('.',explode('/', $folderPath)[count(explode('/', $folderPath)) - 1])[0]; 
            $moduleName     = explode(" ",explode("=",$getRealData)[0]);
            $moduleName     = $moduleName[count($moduleName) - 2];
            $JSON_data      = $this->convertJSON($getRealData);
            $convertJson    = json_decode($JSON_data);
            if (!$convertJson) {
                $this->error(Message::key('convert_json').$folderPath);
            }
            $arrayData      = $this->objectToArray($convertJson);
            $moduleName     = $moduleName == $fileLocale ? 'locale' : $moduleName;
            $this->speratedByKeyAndValue($arrayData, $path, $fileLocale, $moduleName, $results); 
        }
        $headers        = array_merge(['PATH','KEY'],$env_data['env_locale_language']);
        $this->exportToXls($headers,$results, 'Xlsx', $env_data['env_locale_language'], $env_data['env_directory'].'/'.$env_data['env_project_directory']);
        $this->info(Message::key('export_success'));
        
    }

    /**
     * Normal string convert to JSON
     */
    public function convertJSON ($getRealData)
    {
        $realData       = explode("=", $getRealData);
        $realData       = explode(";", $realData[1]);
        $realData       = str_replace("\\t" , "",  stripslashes($realData[0])); 
        $realData       = trim(preg_replace('/\t+/', '', $realData));
        $realData       = preg_replace("/\s+/", " ", $realData);
        $realData       = stripslashes(preg_replace('/(\w+)\s{0,1}:/', '"\1":', str_replace(array("\r\n", "\r", "\n", "\t"), "", $realData)));
        $realData       = str_replace(', }',"}",$realData);
        return $realData;
    }


    /**
     * Convert Array based on key
     */
    public function speratedByKeyAndValue ($arrayData, $path, $fileLocale, $moduleName, &$results) {
        foreach ($arrayData as $key => $value) {
            if (is_array($value)) {
                $this->speratedByKeyAndValue($value, $path, $fileLocale, $moduleName.'.'.$key, $results);
            } else {
                $results[$path][$moduleName.'.'.$key][$fileLocale] = $value;
            }
        }
        return $results;
    }

    /**
     * Filter the File Based on Language Array
     */
    public function filterFiletoLanguage ($resultData, $languageData) { 
        $results = array();
        foreach ($resultData as $filePath) {
            foreach ($languageData as $name) {
                if (stripos($filePath,'/'.$name)) {
                    $results[] = $filePath;
                }
            }
        }
        return $results;
    }

    /**
     * Get All File Form particular folder filter
     */
    public function getAllLanguageFile($dir, $folders, &$results = array()) 
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                foreach ($folders as $folderName) {
                    if (stripos($path,$folderName.'/')) {
                        $results[] = $path;
                    }
                }
            } else if ($value != "." && $value != "..") {
                $this->getAllLanguageFile($path, $folders, $results);
                foreach ($folders as $folderName) {
                    if (stripos($path,$folderName.'/')) {
                        $results[] = $path;
                    }
                }
            }
        }
        return $results;   
    }

    /**
     * Convert Objec to Array
     */
    public function objectToArray($result)
    {
        $array = array();
        if (is_object($result) || is_array($result)) {
            foreach ($result as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $array[$key] = $this->objectToArray($value);
                } else {
                    $array[$key] = $value;
                }
            }
        }
        return $array;
    }

    /**
     * Report Generator Array to Excel
     */
    public function exportToXls($headers = array(),$results = array(),$ext = 'Xlsx', $language = array(), $filePath)
    {  
        if ($results) {
            $topHeader   = range('A', 'Z');
            $objPHPExcel = new Spreadsheet();

            $setHeader     = $objPHPExcel->setActiveSheetIndex(0);
            $headerFormats = array('font' => array('bold' => true, 'color' => array('rgb' => '704cff'), 'size' => 14, 'name' => 'calibri'));
            $fieldsFormats = array('font' => array('size' => 12, 'name' => 'calibri'));
            $styleBorder   = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));

            $headerAlign = array_merge($headerFormats, $styleBorder);
            $fieldsAlign = array_merge($fieldsFormats, $styleBorder);

            for ($i = 0; $i < count($headers); $i++) { 
                $setHeader->setCellValue($topHeader[$i] . '1', $headers[$i]);
                $objPHPExcel->getActiveSheet()->getStyle($topHeader[$i] . '1')->applyFromArray($headerAlign);
            }
            $setValue  = $objPHPExcel->setActiveSheetIndex(0);
            $cellvalue = 2;
            foreach ($results as $module => $data) {
                foreach ($data as $key => $langArray) {
                    $setValue->setCellValue($topHeader[0] . $cellvalue, @$module);
                    $setValue->setCellValue($topHeader[1] . $cellvalue, @$key);
                    $j =2;
                    foreach ($language as $lang) { 
                        $setValue->setCellValue($topHeader[$j] . $cellvalue, @$langArray[$lang]);
                        $j++;
                    }
                    $cellvalue++;
                }
                $objPHPExcel->getActiveSheet()->getStyle($topHeader[0] . $cellvalue)->applyFromArray($fieldsAlign);
            }

            $objPHPExcel->setActiveSheetIndex(0);
            $sheet        = $objPHPExcel->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell - To auto ajustment for the every cells dynamically **/
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
            if (ob_get_length() > 0) { 
                ob_end_clean(); 
            }
            $locationPath = $filePath.'/'.explode('/',$filePath)[count(explode('/',$filePath)) - 1].'translationFile.'.$ext;
            if (file_exists($locationPath)) {
                unlink($locationPath);
            }
            $writer = IOFactory::createWriter($objPHPExcel, $ext);
            $writer->save($locationPath);
        }
    }
}