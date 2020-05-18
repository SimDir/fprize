<?php defined('ROOT') OR die('No direct script access.');

return array(
    '^page/([-_a-zA-Z0-9]+.html)' => 'page/page/$1',
    '^error/([0-9]+)' => 'index/eror/$1',
    'client.php' => 'index/client',
    'partner.php' => 'index/partners',
    '\bjs\b/([-_a-z0-9]+)' => 'res/js/$1',
//    '\bimg\b' => 'res/img/',
//    '\bimg\b/([-_a-z0-9]+)' => 'res/img/$1'
);
