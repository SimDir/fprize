<?php defined('ROOT') OR die('No direct script access.');
/**
 * возможные значения db_driver
 * 
 *      MariaDB
 *      PostgreSQL
 *      SQLite
 *      CUBRID
 * 
 * подробнее смотрите описание 
 * https://redbeanphp.com/index.php?p=/connection
 * 
 * если не знаете что поставить просто поставте значение 'db_driver' в SQLite
 * в этом случии вусе остальные поля будут проигнорированный 
 * 
 */
return array(
    'db_driver' => 'MariaDB',
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_frozen' => false,
    'db_name' => 'dbname',
    'db_login' => 'dblogin',
    'db_pass' => 'dbpass'
);
