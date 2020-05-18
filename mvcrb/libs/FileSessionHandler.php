<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of FileSessionHandler
 *
 * @author Ivan Kolotilkin
 * 
 */
class FileSessionHandler {

    private $savePath;
    
    function open($savePath, $sessionName) {
//        dd($sessionName);
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    function close() {
        return true;
    }

    function read($id) {
        $sesFile = "$this->savePath/sess_$id";
        if(file_exists($sesFile)){
            return mvcrb::StrDecrypt(file_get_contents($sesFile), mvcrb::BrouserHash());
        }
        return '';
    }

    function write($id, $data) {
        $DataCrypt = mvcrb::StrEncrypt($data, mvcrb::BrouserHash());
        return file_put_contents("$this->savePath/sess_$id", $DataCrypt) === false ? false : true;
    }

    function destroy($id) {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    function gc($maxlifetime) {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

}
