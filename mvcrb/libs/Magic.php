<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');
/**
 * Description of Magic
 *
 * @author Ivan P Kolotilkin
 */
abstract class Magic {
    private static $ClassArray=[];
    
    public function GetModel(string $Model):Model{
        if(isset(self::$ClassArray[$Model])){
            return self::$ClassArray[$Model];
        }
        
        if (strpos($Model, 'Model') === false) {
            $ModelClass = __NAMESPACE__. '\\'.$Model.'Model';
        } else {
            $ModelClass = __NAMESPACE__. '\\'.$Model;
        }
        
        if(class_exists($ModelClass)){
            
            self::$ClassArray[$Model] = new $ModelClass();
//            dd(self::$ClassArray[$Model]);
            return self::$ClassArray[$Model] = new $ModelClass();
        }else{
            if (isset(self::$ClassArray[$Model])) {
                return self::$ClassArray[$Model];
            }
            return self::$ClassArray[$Model] = new ControllerModel($Model);
        }
        
    }
    public function __get(string $name) {
        
        switch ($name) {
            case 'MODEL':
            case 'Model':
            case 'model':    
                $ModelName = mb_strtolower(str_replace('Controller','',end(explode('\\', get_class($this)))));
                if (isset(self::$ClassArray[$ModelName])) {
                    return self::$ClassArray[$ModelName];
                }
                return self::$ClassArray[$ModelName] = new ControllerModel($ModelName);

            case 'VIEW':
            case 'View':
            case 'view':
//                if (isset(self::$ClassArray['view'])) {
//                    return self::$ClassArray['view'];
//                }
                return View::getInstance(end(explode('\\', get_class($this))));
//                break;
            case 'REQUEST': 
            case 'Request':
            case 'request':
                return file_get_contents('php://input');
            case 'CONFIG': 
            case 'Config':
            case 'config':
                return mvcrb::Config();    
            default:
                return null;
        }
        
    }
}
