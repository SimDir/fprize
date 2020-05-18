<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of PageModel
 *
 * @author Ivan Kolotilkin
 */
class PageModel extends Model {

    private $TableName;

    public function __construct() {
        parent::__construct();
        $this->TableName = 'page';
    }

    public function GetList($PostData = null) {
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
            $tempbean = $this->getAll('SELECT id,name,title,editdatetime,type FROM ' . $this->TableName . ' ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->getAll('SELECT id,name,title,editdatetime,type FROM ' . $this->TableName . ' LIMIT ' . $start . ', ' . $limit);
        }
//        dd($tempbean);
        if ($tempbean) {

            $List['data'] = $tempbean; //$this->exportAll($tempbean, TRUE);
            return $List;
        }
        return FALSE;
    }
    public function GetAllPage($PostData = null) {


        $tempbean = $this->getAll('SELECT name,title,createdatetime,type FROM ' .$this->TableName . ' ORDER BY createdatetime' );
        
//        dd($tempbean);
        if ($tempbean) {
            return $tempbean;//$this->exportAll($tempbean, TRUE);

        }
        return FALSE;
    }
    public function Add($Data = null) {
        if (is_null($Data))
            return false;
        $Table = $this->Dispense($this->TableName);
        $Table->import($Data);
        $Table->createdatetime = date('Y-m-d H:i:s');
        $Table->editdatetime = date('Y-m-d H:i:s');
        return $this->store($Table);
    }
    public function Del($id = null) {
        if (is_null($id))
            return false;
        $Page = $this->load($this->TableName, $id);
        return $this->trash($Page);
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

    public function GetPage($name = '') {
        $Ret = $this->findOne($this->TableName, '(name = :idname) OR (id = :idname)', [':idname' => $name]);
        if ($Ret) {
            return $Ret->export();
        }
        return FALSE;
    }

}
