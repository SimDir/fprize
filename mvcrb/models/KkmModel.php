<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of KkmModel
 *
 * @author Ivan Kolotilkin
 */
class KkmModel extends Model {

    private $TableName;

    public function __construct() {
        parent::__construct();
        $this->TableName = 'kkm';
    }
    public function GetAllKkm() {
        return $this->exportAll($this->findAll($this->TableName, " INNER JOIN `user` WHERE `user`.`status`='verefid' AND `kkm`.`user_id`=`user`.`id` AND `user`.`role`=300"), TRUE);
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
    
    public function getUserKkm($UserId) {
        $User = $this->load( 'user' ,$UserId);
        return $User->ownKkmList;
    }
    public function Add($Data = null, int $UserId=0) {
        if (is_null($Data))
            return ['Error' => 'not data to add'];
        $Ret = $this->findAll($this->TableName, '(fn = :fn)', [':fn' => $Data['fn']]);
        if ($Ret) {
            return ['Error' => 'ошибка: ККМ уже добавлена ранее'];
        }
        $User = $this->load( 'user' ,$UserId);
        
        $Table = $this->Dispense($this->TableName);
//        $Table->import($Data);
        $Table->address = $Data['address'];
        $Table->fn = (string)$Data['fn'];
        $Table->desc = $Data['desc'];
        $Table->name = $Data['name'];
        $Table->createdatetime = date('Y-m-d H:i:s');
        $User->ownKkmList[] = $Table;
        $this->store($User);
        return ['success'=>'ККМ успешно добавлена под номером ID '.$this->store($Table)];
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
//        return $this->store($Table);
        return ['success'=>'KKM is saved ID '.$this->store($Table)];
    }

}
