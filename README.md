<p align="center">
  <img src="https://raw.githubusercontent.com/antony382/roles-and-permission/master/public/images/logo.png" style="width: 15% !important;max-width: 20% !important;">
</p>

ProcessDrive JS Translation Converter
=====================================



[![Latest Stable Version](https://poser.pugx.org/process-drive/translation-drive/v/stable)](https://packagist.org/packages/process-drive/translation-drive)
[![License](https://poser.pugx.org/process-drive/translation-drive/license)](https://packagist.org/packages/process-drive/translation-drive)
[![Total Downloads](https://poser.pugx.org/process-drive/translation-drive/downloads)](https://packagist.org/packages/process-drive/translation-drive)
[![Monthly Downloads](https://poser.pugx.org/process-drive/translation-drive/d/monthly)](https://packagist.org/packages/process-drive/translation-drive)
[![Daily Downloads](https://poser.pugx.org/process-drive/translation-drive/d/daily)](https://packagist.org/packages/process-drive/translation-drive)



Convert JS file as excel sheet and excel sheet convert to JS files


Installation
============

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).



run

```
composer require process-drive/translation-drive
```

Either run

```
composer require process-drive/translation-drive:v1.0.0
```

or run

```
composer require process-drive/translation-drive:"Set your version"
```

to the require section of your `composer.json` file.


.env
=====

```
JS_LOCALES      = "en,no,lt,sv,pl"
JS_KEY_STRING   = "no"
DIRECTORY       = "resources"
JS_PROJECT_PATH = "checklist"
FIND_DIRECTORY  = "language,languages"
```
```
JS_LOCALES    = Set your locales files
JS_KEY_STRING = Key values render in JS File
DIRECTORY     = Mail folder name 
JS_PROJECT_PATH = JS project file
FIND_DIRECTORY = Read Language folders
```

```
'resources' => {
    'checklist' => {
        'language' => {
          en.js
          no.js
        }
        'languages' => {
          en.js
          no.js
        }
    }
}
```
After Instalization
===================
First Export your JS file and then work on it.

License
=======



MIT
