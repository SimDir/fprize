<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Главный класс всего приложения
 * 
 */
function dd($str) {
    dump($str);
//    var_dump($str);
    die();
}
//https://swiftmailer.symfony.com/docs/sending.html
function rr_mail(string $to , string $subject , string $messages, $additional_headers=null){
    try {
        // Create the SMTP Transport
        $transport = (new \Swift_SmtpTransport('smtp.beget.com', 465))
                ->setUsername('info@fprize.ru')
                ->setPassword('Info73501505')->setEncryption('SSL');// jsU3Kr&k beget
//$transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
//$transport = new \Swift_SmtpTransport('smtp.yandex.ru', 587, 'ssl');
        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = new \Swift_Message();

        // Set a "subject"
        $message->setSubject($subject);

        // Set the "From address"
        $message->setFrom(['info@fprize.ru' => 'Робот отправки сообщений']);

        // Set the "To address" [Use setTo method for multiple recipients, argument should be array]
        $message->addTo($to, 'получатель '.$to);

        // Add "CC" address [Use setCc method for multiple recipients, argument should be array]
//        $message->addCc('recipient@gmail.com', 'recipient name');

        // Add "BCC" address [Use setBcc method for multiple recipients, argument should be array]
//        $message->addBcc('recipient@gmail.com', 'recipient name');

        // Add an "Attachment" (Also, the dynamic data can be attached)
//        $attachment = Swift_Attachment::fromPath('example.xls');
//        $attachment->setFilename('report.xls');
//        $message->attach($attachment);

        // Add inline "Image"
//        $inline_attachment = Swift_Image::fromPath('nature.jpg');
//        $cid = $message->embed($inline_attachment);

        // Set the plain-text "Body"
        $message->setBody($messages);

        // Set a "Body"
        $message->addPart('Это сообщение отправленно роботом.<br>'.$messages.'<br>Не отвечайте на это сообщение', 'text/html');

        // Send the message
        return $mailer->send($message);
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
class mvcrb {

    public static $ErrorMessage;
    Private static $globalConfig = [];
    private static $ExecRetVal;
    /*
     * Переменные роутинга 
     */
    public static $URI = ''; // Строка УРЛ запроса  site.com/Controller/Action/Param1/Param2/Param3/ и так далее
    Private static $ControllerName; // Имя выполняемого контроллера <Controller>
    Private static $ActionName; // Имя выполняемого метода <Action>
    Private static $ControllerFile; // подключаемый фаил контроллера <...\ControllerPath\*Name*Controller.php>
    Private static $ParametersArray; // массив параметров которые пришли в УРЛ строке

    public static function ErrorMessage() {
        return self::$ErrorMessage;
    }

    private static function initWhoops() {
        $whoops = new \Whoops\Run;
        $whoops_pretty_page_handler = new \Whoops\Handler\PrettyPageHandler();
        $whoops_pretty_page_handler->setEditor('vscode');
        $whoops->pushHandler($whoops_pretty_page_handler);
        
        $monolog_multiline_formatter = new \Monolog\Formatter\LineFormatter(null, null, true);
        $monolog_error_log_handler = new \Monolog\Handler\ErrorLogHandler();
        $monolog_error_log_handler->setFormatter($monolog_multiline_formatter);
        $monolog_logger_error_log = new \Monolog\Logger('whoops_logger', [$monolog_error_log_handler]);
        $monolog_logger_error_log->pushHandler(new \Monolog\Handler\StreamHandler(SITE_DIR.'error.log'));
        
        
        $whoops_plain_text_handler = new \Whoops\Handler\PlainTextHandler();
        $whoops_plain_text_handler->loggerOnly(true);
        $whoops_plain_text_handler->setLogger($monolog_logger_error_log);
        $whoops->pushHandler($whoops_plain_text_handler);
        $monolog_browser_console_handler = new \Monolog\Handler\BrowserConsoleHandler();
        $monolog_browser_console_handler->setFormatter($monolog_multiline_formatter);
        $monolog_browser_console_logger = new \Monolog\Logger('whoops_browser_console_logger', [$monolog_browser_console_handler]);
        $whoops_plain_text_handler2 = new \Whoops\Handler\PlainTextHandler();
        $whoops_plain_text_handler2->loggerOnly(true);
        $whoops_plain_text_handler2->setLogger($monolog_browser_console_logger);
        $whoops->pushHandler($whoops_plain_text_handler2);
        $whoops->register();
    }
    /**
     * Метод запускает крон задачу приложения. 
     * 
     */
    public static function RunCron() {
//        echo 'Cron service start';
        self::SetupConfig();

        self::InitAutoload();
        
        $errorHandler = new ErrorHandler;
        $errorHandler->register();
        echo Session::GarbageCollector();
//        echo 'Cron service stop';
    }
    /**
     * Основной метод запускает все приложение. так называемая точка входа
     * 
     */
    public static function Run() {
        if (SHOW_ERROR) {
            self::initWhoops();
        } else {
            include_once APP.'libs'.DS.'ErrorHandler.php';
            $errorHandler = new ErrorHandler;
            $errorHandler->register();
        }
        self::SetupConfig();
//        dd(self);
        self::InitAutoload();
        
        self::GetControllerAndAction();
        self::$ExecRetVal = self::Exec(self::$ControllerName, self::$ActionName, self::$ParametersArray);
        if (is_string(self::$ExecRetVal)) {
            echo self::$ExecRetVal;
        } else {
            if (!headers_sent())
                header('Content-type: application/json');
            echo json_encode(self::$ExecRetVal);
        }
    }

    public static function InitAutoload() {
        spl_autoload_register(__CLASS__ . '::AutoLoadClassFile');
    }

    public static function Exec($Controller = '', $Action = '', $Param = []) {

        $ctrl = 'mvcrb\\' . $Controller;

        if (class_exists($ctrl)) {
            $objectCtrl = new $ctrl();
            if (method_exists($objectCtrl, $Action)) {

                if (count($Param)) {
                    return call_user_func_array(array($objectCtrl, $Action), $Param);
                } else {
                    return call_user_func(array($objectCtrl, $Action));
                }
            }
            if (SHOW_ERROR) {
                if (!headers_sent()) {
                    header("HTTP/1.1 405 Method Not Allowed");
                    header("Status: 405 Method Not Allowed");
                }
                return "<h1>405 Method Not Allowed</h1>" . __METHOD__ . "<h5> Контроллер <b style=\"color: red;\">" . $Controller . "</b> Не имеет метода <b style=\"color: red;\">$Action()</b></h5>";
            } else {
                return self::Redirect(ERROR_URL);
            }
        }
        if (SHOW_ERROR) {
            if (!headers_sent()) {
                header("HTTP/1.1 523 Origin Is Unreachable");
                header("Status: 523 Origin Is Unreachable");
            }
            return "<h1>523 Origin Is Unreachable</h1>" . __METHOD__ . "<h5> Нет исполнительного контроллера <b style=\"color: red;\">$Controller</b></h5>";
        } else {
            return self::Redirect(ERROR_URL);
        }
    }

    /**
     * функция получения запроса который пришел от пользователя приложением
     * @return String
     */
    private static function GetURI() {
        $pathInfo = filter_input(INPUT_SERVER, 'PATH_INFO');
        if ($pathInfo) {
            $path = $pathInfo;
        } else {
            $requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
            if (strpos($requestURI, '?')) {
                $requestURI = substr($requestURI, 0, strpos($requestURI, '?'));
            } elseif (strpos($requestURI, '&')) {
                $requestURI = substr($requestURI, 0, strpos($requestURI, '&'));
            }
            $path = trim($requestURI);
        }
//        dd($path);
        if (!$path) {
            $path = '/';
        }
        $path = parse_url($path);
        self::$URI = trim($path['path'], '/');
        return self::$URI;
    }

    /**
     * Получаем контроллер и метод.
     * данная функция находит в УРЛ тот контроллер и метот на который пршол запрос
     * заполныет переменные роутинга
     * и если необходимо настраевает пришедший в УРЛ запрос
     * @return Boolean
     */
    private static function GetControllerAndAction() {
        $access = false;
        self::GetURI();
        $cfg = self::$globalConfig['App_Config_Dir'] . self::$globalConfig['App_Router_Config_File'];
        if (file_exists($cfg)) {
            $routes = include($cfg);
        } else {
//            throw new \Exception(__METHOD__ . " Конфигурационный фаил роутинга $cfg не найден. продолжить невозможно");
//            return false;
            $routes = [];
        }

        // проверяю запрос на соответствие регулярному выражению
        foreach ($routes as $uriPattern => $path) {
            if (!preg_match("~$uriPattern~", self::$URI)) {
                continue;
            }
            // получаем внутренний путь из внешнего согласно правилам маршрутизации
            $access = preg_replace("~$uriPattern~", $path, self::$URI);
        }
        if (!$access) {
            if (empty(self::$URI)) {
                $access = self::$globalConfig['Router_Default_Controller'] . "/" . self::$globalConfig['Router_Default_Action']; //
            } else {
                $access = self::$URI;
            }
        }
        $segments = explode('/', $access);
        $dirForControllers = self::$globalConfig['App_Controllers_Dir'];
        $controlerName = ucfirst(array_shift($segments)) . 'Controller';
        self::$ControllerName = $controlerName;
        $controllerFile = $dirForControllers . $controlerName . '.php';
        $action = ucfirst(array_shift($segments));
        if (empty($action)) {
            $action = ucfirst(self::$globalConfig['Router_Default_Action']);
//            var_dump($this->globalConfig['Router_Default_Action']);
        }
        $actionName = $action . 'Action';
        self::$ActionName = $actionName;
        self::$ParametersArray = $segments;
        if (!file_exists($controllerFile)) {
//            if (file_exists($dirForControllers) && is_dir($dirForControllers)) {
//                die('Директория с контроллерами не найдена');
//            }
            $dirArray = scandir($dirForControllers);
            foreach ($dirArray as $da) {
                if (is_dir($dirForControllers . $da)) {
                    $controllerFile = $dirForControllers . $da . DIRECTORY_SEPARATOR . $controlerName . '.php';
                    if (file_exists($controllerFile)) { // если в текущей подпапке есть контроллер
                        self::$ControllerFile = $controllerFile;
                        return $controllerFile; // и выходим из функции
                    }
                }
            }

//            header('HTTP/1.0 404 Not Found');
//            exit('Нет контроллера '.$controlerName);
//            throw new \Exception(__METHOD__ . ' [ERROR:404] фаил Контроллера ' . $controlerName . '.php не найден');
            return FALSE;
        } else {
            self::$ControllerFile = $controllerFile;
            return $controllerFile;
        }
//        require_once ($controllerFile);
//        throw new \Exception(__METHOD__ . ' [ERROR:404] фаил Контроллера ' . $controlerName . '.php не найден');
        return FALSE;
    }

    public static function Config() {
        return self::$globalConfig;
    }

    /**
     * настраиваем основную конфигурацию ядра системы
     */
    private static function SetupConfig() {
        self::$globalConfig['App_Name'] = __NAMESPACE__;
        self::$globalConfig['App_Dir'] = APP;
        self::$globalConfig['App_lib_Dir'] = self::$globalConfig['App_Dir'] . 'libs' . DIRECTORY_SEPARATOR;
        self::$globalConfig['App_Config_Dir'] = CONFIG_DIR;
        self::$globalConfig['App_Controllers_Dir'] = self::$globalConfig['App_Dir'] . 'controllers' . DIRECTORY_SEPARATOR;
        self::$globalConfig['App_Models_Dir'] = self::$globalConfig['App_Dir'] . 'models' . DIRECTORY_SEPARATOR;
        self::$globalConfig['App_User_locale'] = filter_input(INPUT_SERVER, "HTTP_ACCEPT_LANGUAGE", FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        self::$globalConfig['App_Templates_Dir'] = TEMPLATE_DIR; // . 'default' . DIRECTORY_SEPARATOR;

        if (!file_exists(self::$globalConfig['App_Config_Dir'] . 'AutoLoader.php')) {
//            exit('AutoLoaderConfig.php не найден');
            self::$globalConfig['App_Clas_Loader_Dir_Array'] = ['libs', 'models', 'controllers'];
        } else {
            self::$globalConfig['App_Clas_Loader_Dir_Array'] = include_once self::$globalConfig['App_Config_Dir'] . 'AutoLoader.php';
        }

        self::$globalConfig['App_Router_Config_File'] = 'Routes.php';
        self::$globalConfig['Router_Default_Controller'] = 'index';
        self::$globalConfig['Router_Default_Action'] = 'index';
//        $str_value = serialize(self::$globalConfig);
//        echo $str_value;
    }

    /**
     * Автоматическая загрузка классов
     * @param type $className
     * @return boolean
     */
    private static function AutoLoadClassFile($className) {
        $parts = explode('\\', $className);
        $class = end($parts);
//        file_put_contents(SITE_DIR.'alog.txt', $class.PHP_EOL,FILE_APPEND);
        $classFile = self::$globalConfig['App_lib_Dir'] . $class . '.php';
        if (file_exists($classFile)) {
            include_once $classFile;
            return $classFile;
        }
        return self::LoadClassFileForAllDir($class);
    }

    private static function LoadClassFileForAllDir($className) {
//        echo $className;
//        self::$loadClassArray[] = $className;
        $dirArr = self::$globalConfig['App_Clas_Loader_Dir_Array'];
        $appDir = self::$globalConfig['App_Dir'];
        foreach ($dirArr as $value) {
            $classFile = self::SearchFile($className . '.php', $appDir . $value);
            if (file_exists($classFile)) {
                include_once $classFile;
                return $classFile;
            }
        }
        return FALSE;
    }

    /**
     * Поиск файла по имени во всех папках и подпапках
     * @param string $fileName - искомый файл
     * @param string $folderName - пусть до папки
     */
    public static function SearchFile($fileName, $folderName) {
        // перебираем пока есть файлы
        if (!is_dir($folderName)) {
            return false;
        }
        $dirArray = scandir($folderName);
        foreach ($dirArray as $file) {
            if ($file != "." && $file != "..") {
                // если файл проверяем имя
                if (is_file($folderName . DIRECTORY_SEPARATOR . $file)) {
                    // если имя файла искомое,
                    // то вернем путь до него
                    if ($file == $fileName) {
                        return $folderName . DIRECTORY_SEPARATOR . $file;
                    }
//                    echo $folderName.'\\'.$file.'<br>';
                }
                // если папка, то рекурсивно
                // вызываем SearchFile
                if (is_dir($folderName . DIRECTORY_SEPARATOR . $file)) {
                    $retVal = self::SearchFile($fileName, $folderName . DIRECTORY_SEPARATOR . $file);
                    if ($retVal) { // если фуекция что-то вернула то выходим
                        return $retVal;
                    }
                }
            }
        }
    }

    public static function Redirect($url, $permanent = false) {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }
        return '<script type="text/javascript">window.location = "' . $url . '"</script>';
    }
// Encrypt Function
    public static function StrEncrypt($encrypt, $key) {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($encrypt, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

// Decrypt Function
    public static function StrDecrypt($decrypt, $key) {
        $c = base64_decode($decrypt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))
        {
            return $plaintext;
        }
    }
    public static function BrouserHash() {
        return md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
}
