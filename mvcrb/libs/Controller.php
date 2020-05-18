<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of Controller
 *
 * @author Ivan P Kolotilkin
 */
abstract class Controller extends Magic{

    public $GET = FALSE;
    public $POST = FALSE;
    public $REQUEST_METHOD = FALSE;

    public function __construct() {
        $this->REQUEST_METHOD = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
        switch ($this->REQUEST_METHOD) {
            case 'GET':
                $this->GET = TRUE;
                break;
            case 'POST':
                $this->POST = TRUE;
                break;
        }
        return $this;
    }

}
