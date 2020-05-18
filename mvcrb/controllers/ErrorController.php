<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of ErrorController
 *
 * @author mvcrb
 */
class ErrorController {

    //put your code here
    public function IndexAction($param = 0) {
        if (!headers_sent()) {
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
        }
        $view = new View(SITE_DIR.'ErrorController'.DS);
        return $view->execute('err.html', SITE_DIR.'Front'.DS. 'ErrorController'.DS);
//        return '<H1>'.$param.'</H1>';
    }

}
