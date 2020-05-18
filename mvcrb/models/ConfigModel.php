<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of ConfigModel
 *
 * @author ivank
 */
class ConfigModel extends Model {

    private $TableName;

    public function __construct() {
        parent::__construct();
        $this->TableName = 'config';
    }

    public function GetCount() {
        return $this->count($this->TableName);
    }
    public function GetSetting($name = false) {
        
        $tempbean = $this->findOne($this->TableName, "(name = :name)", [':name' => $name]);
        
        return $tempbean->export()['value'];
    }

    public function AddSetting($Data = null) {
        if (is_null($Data))
            return false;
        $Table = $this->Dispense($this->TableName);
        $Table->import($Data);
//        $Table->createdatetime = date('Y-m-d H:i:s');
        return $this->store($Table);
    }

    public function SaveSetting($name,$value) {
        
        $id = $this->findOne($this->TableName, ' name = ? ', [$name]);
        if ($id > 0) {
            $Table = $this->load($this->TableName, $id);
        } else {
            $Table = $this->Dispense($this->TableName);
        }
        $Table->name=$name;
        $Table->value=$value;
        return $this->store($Table);
    }

}
