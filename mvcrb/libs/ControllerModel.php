<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of ControllerModel
 *
 * @author Ivan Kolotilkin
 */
class ControllerModel extends Model {

    public $TableName;

    public function __construct($ModelName = '') {
        parent::__construct();
        $this->TableName = $ModelName;
        return $this;
    }

    public function List($PostData = null) {
        $start = $PostData->start ? $PostData->start : 0;
        $limit = $PostData->limit ? $PostData->limit : 10;
        $List['count'] = $this->count($this->TableName);
        if (isset($PostData->data) && $PostData->data !== '') {
            $order['data'] = $PostData->data;
            $order['dir'] = $PostData->dir;
        } else {
            $order = null;
        }

        if (is_array($order)) {
            $tempbean = $this->findAll($this->TableName, ' ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->findAll($this->TableName, ' LIMIT ' . $start . ', ' . $limit);
        }
//        dd($tempbean);
        if ($tempbean) {

            $List['data'] = $this->exportAll($tempbean, TRUE);
            return $List;
        }
        return FALSE;
    }

    public function Edit($Data = null, $id) {
        if (is_null($Data))
            return false;
//        $Table = $this->Dispense($this->TableName);
        $Table = $this->findOne($this->TableName, 'id = ?', array($id));
        $Table->import($Data);
        $Table->editdatetime = date('Y-m-d H:i:s');
        return $this->store($Table);
    }

    public function Dell(int $id = 0) {
        return $this->trash($this->load($this->TableName, $id));
    }

    public function Get($name = '') {
        $Ret = $this->findOne($this->TableName, '(name = :idname) OR (id = :idname)', [':idname' => $name]);
        if ($Ret) {
            return $Ret->export();
        }
        return FALSE;
    }

    public function Counts() {
        return parent::count($this->TableName);
    }

}
