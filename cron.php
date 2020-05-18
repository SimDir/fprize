<?php
/**
 * Copyright 2019 Ivan P. Kolotilkin
 * 
 * logic@xaker.ru
 * 
 * +79372796383
 * 
 * https://vk.com/id131505651
 * 
 * https://github.com/SimDir/RusBeard
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/
//define('TIME_START', microtime(true));// для подсчета времени работы скрипта
//define('USE_MEM', memory_get_usage()); // тоже самое только для используемой памяти сервера
if (version_compare(phpversion(), '7.1.0', '<') == true) {
    die('на сервере версия PHP меньше 7.1 продолжить невозможно. обновите версию PHP');
}
define('SESSION_PREFIX', 'Agatech_session_');
define('DS', DIRECTORY_SEPARATOR); // разделитель для путей к файлам
define('ROOT', dirname(__FILE__)); // защита всех файлов приложения от прямого доступа к ним
define('SITE_DIR', realpath(dirname(__FILE__)) . DS); // путь к корневой папке сайта getcwd()
define('APP', SITE_DIR . 'mvcrb' . DS); // путь к приложению
//define('TEMPLATE_DIR', SITE_DIR . 'portal' . DS . 'dist' . DS); // путь до файлов до шаблонами
define('TEMPLATE_DIR', SITE_DIR . 'Front' . DS);

define('CONFIG_DIR', SITE_DIR . 'Config' . DS); // папка с конфигами

define ('SHOW_ERROR', false); // Показывать ошибки контроллеров или перенаправлять на 404 страницу? https://natribu.org/ru/
define ('ERROR_URL', '/error/index/404');
//define ('ERROR_URL', 'https://natribu.org/ru/');
define ('COMPOSER', SITE_DIR.'vendor'.DS.'autoload.php');

if(file_exists(COMPOSER)){
    require_once COMPOSER; // подключаем композер
}else{
    die('на сервере отсутствует дириктория "vendor" а это значит "Composer" не установлен! продолжить невозможно. установите composer и обновите бибилиотеки. https://getcomposer.org/');
}
require_once APP . 'mvcrb.php';
mvcrb\mvcrb::RunCron();