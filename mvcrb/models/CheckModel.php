<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

use GuzzleHttp\Client;
/**
 * Description of PageModel
 *
 * @author Ivan Kolotilkin
 */
class CheckModel extends Model {

    private $TableName;


    public function __construct() {
        parent::__construct();
        $this->TableName = 'check';
    }
    public function Temp() {
        // Временная функция просто подогнал старые данные под новый формат. 
//        $tempbean = $this->getAll('SELECT * FROM `check` WHERE user_id IS NULL');
        $tempbean = $this->findAll($this->TableName,'WHERE client_id IS NULL');
        foreach ($tempbean as $value) {
            preg_match("/fn=(\w+)/", $value->qr, $fn);
            $value->fn = $fn[1];
            $value->user_id = 1;
            $this->store($value);
            //return $value;
        }
        return $tempbean;
    }
    public function GetMyList($UserID = null) {
        $tempbean = $this->getAll('SELECT * FROM `check` WHERE `client_id`='.(int)$UserID);
        return $tempbean;
    }
    public function wList($PostData = null) {
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
            $tempbean = $this->getAll('SELECT `user`.id,`user`.surname,`user`.firstname,`user`.lastname,`check`.client_id,`check`.fn,`check`.qr,`check`.createdatetime FROM `user`,`check` WHERE `user`.id = `check`.client_id ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
//            dd($tempbean);
            //$tempbean = $this->findAll($this->TableName, ' ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->getAll('SELECT `user`.id,`user`.surname,`user`.firstname,`user`.lastname,`check`.client_id,`check`.fn,`check`.qr,`check`.createdatetime FROM `user`,`check` WHERE `user`.id = `check`.client_id LIMIT '. $start . ', ' . $limit);
            //$tempbean = $this->findAll($this->TableName, ' LIMIT ' . $start . ', ' . $limit);
        }
//        dd($tempbean);
        if ($tempbean) {

            $List['data'] = $tempbean;
//            $List['data'] = $this->exportAll($tempbean, TRUE);
            return $List;
        }
        return FALSE;
    }
    
    public function uList($PostData = null,int$id=0) {
        $start = $PostData->start ? $PostData->start : 0;
        $limit = $PostData->limit ? $PostData->limit : 10;
//        $List['count'] = $this->count($this->TableName);
        if (isset($PostData->data) && $PostData->data !== '') {
            $order['data'] = $PostData->data;
            $order['dir'] = $PostData->dir;
        } else {
            $order = null;
        }
        if (is_array($order)) {
            $tempbean = $this->getAll('SELECT `check`.`createdatetime`,`check`.`qr`,`check`.`fn`,`check`.`client_id`,`kkm`.`id` FROM `check` INNER JOIN `kkm` WHERE `check`.`fn`=`kkm`.`fn` AND `kkm`.`user_id`='.$id.' ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->getAll('SELECT `check`.`createdatetime`,`check`.`qr`,`check`.`fn`,`check`.`client_id`,`kkm`.`id` FROM `check` INNER JOIN `kkm` WHERE `check`.`fn`=`kkm`.`fn` AND `kkm`.`user_id`='.$id.' LIMIT ' . $start . ', ' . $limit);
        }
        if ($tempbean) {
            $List['data'] = $tempbean;
            $List['count'] = $this->CheckCount($id);
            return $List;
        }
        return FALSE;
    }
    public function CheckCount(int$id = 0) {
        return $this->getAll('SELECT count(*) as CheckCount FROM `check` INNER JOIN `kkm` WHERE `check`.`fn`=`kkm`.`fn` AND `kkm`.`user_id`=' . $id)[0]['CheckCount'];
    }

    public function Add($Data = null) {
        if (is_null($Data))
            return false;
        $Table = $this->Dispense($this->TableName);
        $Table->import($Data);
        $Table->createdatetime = date('Y-m-d H:i:s');
//        $Table->editdatetime = date('Y-m-d H:i:s');
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

    public function GetAllUserCheck($UserId = '') {
        $Ret = $this->findAll($this->TableName, '(userid = :id)', [':id' => $UserId]);
        if ($Ret) {
            return $Ret->export();
        }
        return FALSE;
    }
    public function AddUserCheck($UserId=0,$UserQrCod='') {
        $Ret = $this->findAll($this->TableName, '(qr = :qr)', [':qr' => $UserQrCod]);
        if($Ret){
            return ['Error'=>'check has already been added. Такой чек уже добавлен'];
        }
        $User = $this->load( 'client' ,$UserId);

        $Table = $this->Dispense($this->TableName);
//        $Table->userid = $UserId;
        $Table->qr = $UserQrCod;
        preg_match("/fn=(\w+)/", $UserQrCod, $fn);
        $fn = $fn[1]; //<номер ФН>
        $Table->fn = (string)$fn;
        $Table->createdatetime = date('Y-m-d H:i:s');
//        dd($fn);
        $User->ownCheckList[] = $Table;
        $this->store( $User );
        $retTable = $this->store( $Table );
        
        return ['Success'=>$retTable];
    }
    public function ValidateCheck($qr='') {
//        return ['Success' => 'is valid'];
        $t ='20200122';// Дата
        $T = '2057';// Время
        $s = '83950';// Сумма
        $fn = '9282440300479231';//<номер ФН>
        $i = '372';//<номер ФД>
        $fp = '205537033';//<номер ФДП>
        $n = '1';//<вид кассового чека
        preg_match("/t=(\w+)T/", $qr, $t);
        preg_match("/T(\w+)/", $qr, $T);
        preg_match("/s=(\w+.\w+)/", $qr, $s);
        
        $s = str_replace('.', '', $s[1]);
//        dd($s);
        preg_match("/fn=(\w+)/", $qr, $fn);
        preg_match("/i=(\w+)/", $qr, $i);
        preg_match("/fp=(\w+)/", $qr, $fp);
        preg_match("/&n=(\w+)/", $qr, $n);
            
        $t =$t[1];// Дата
        $T = $T[1];// Время
        //$s = $s[1];// Сумма
        $fn = $fn[1];//<номер ФН>
        $i = $i[1];//<номер ФД>
        $fp = $fp[1];//<номер ФДП>
        $n = $n[1];//<вид кассового чека   
            // https://irkkt-mobile.nalog.ru:8888/v2/check/ticket?fsId=9282000100262631&operationType=1&documentId=47913&fiscalSign=1815349926&date=2020-09-15T13:55:00&sum=175
        /**
            fsId — ФН
            operationType — тип операции (1 – приход, 2 – возврат прихода, 3 – расход, 4 – возврат расхода)
            documentId — ФД
            fiscalSign — ФП
            date — дата покупки
            sum — сумма чека (без разделителей)
         * 
         * https://github.com/DmitriyBobrovskiy/CheckReceiptSDK/issues/12
         */
        $uT =$t[0].$t[1].$t[2].$t[3].'-'.$t[4].$t[5].'-'.$t[6].$t[7].'T'. $T[0].$T[1].':'.$T[2].$T[3].':'.'00'; // 2020-09-15T13:55:00
        
        $url = "https://irkkt-mobile.nalog.ru:8888/v2/check/ticket?fsId=$fn&operationType=$n&documentId=$i&fiscalSign=$fp&date=$uT&sum=$s";
        $fnsRet = file_get_contents($url);
        if($fnsRet!==false){
            return ['Success' => 'is valid'];
        }
        return ['Error FNS Server' => $fnsRet,
            'qr code'=>$qr,
            'url'=>$url,
            'fns_resp'=>$http_response_header
        ];
//////////////////////////////////////////////////////////////////
        $headers = ['Host' => 'irkkt-mobile.nalog.ru:8888',
            'Accept'=>'*/*',
            'Device-OS' => 'iOS',
            'Device-Id'=>'7C82010F-16CC-446B-8F66-FC4080C66521',
            'clientVersion'=>'2.9.0',
            'Accept-Language'=>'ru-RU;q=1, en-US;q=0.9',
            'User-Agent'=>'billchecker/2.9.0 (iPhone; iOS 13.6; Scale/2.00)',
            'sessionId'=>''];
        
        $auth = ['inn'=>'732716444961','client_secret'=>'IyvrAbKt9h/8p6a7QPh8gpkXYQ4=','password'=>'Stariktz@mail.ru1984'];
        
        $client = new Client([
            'auth' => $auth,
            'headers' => $headers
        ]);
        $response = $client->request('POST', 
            'https://irkkt-mobile.nalog.ru:8888/v2/mobile/users/lkfl/auth', ['json'=>$auth,'http_errors' => false]); 
       
        $b = $response->getBody();
        $sessionId = json_decode($b->getContents(), true)['sessionId'];
        $headers['sessionId']=$sessionId;
        
        $jsonQr=['qr'=>$qr];
        usleep(100);
        $response = $client->request('POST', 
            'https://irkkt-mobile.nalog.ru:8888/v2/ticket', ['json'=>$jsonQr,'headers' => $headers,'http_errors' => false]);
        $b = $response->getBody();
        $ct = $b->getContents();
        /////////////////////
        //

        
//        dd($b->getContents());
        $ticket_id = json_decode($ct, true)['id'];
        $status = json_decode($ct, true);
        
//        dd($status);
        
        if($status['status']==2){
            return ['Success' => 'is valid'];
        }else{
            usleep(1000);
            $response = $client->request('GET', 
                'https://irkkt-mobile.nalog.ru:8888/v2/tickets/'.$ticket_id, ['json'=>$jsonQr,'headers' => $headers,'http_errors' => false]);
            $b = $response->getBody();
            $desk = $b->getContents();
            $status = json_decode($desk, true);
//            foreach ($status as $key => $value) {
//                dd($value);
//            }
            
            if($status[0]['status']==2){
                return ['Success' => 'is valid'];
            }
            return ['Error FNS Server' => $ct,
                'qr code'=>$qr,
                'FNS_Server_response'=>$status,
                'StatusCode' => $response->getStatusCode()
            ];
        
        }
    }
    public function GetWiners() {
//        $client = new Client();
//        $response = $client->request('GET',
//                'http://mylightning.ru/admin/m_request.php?reg=login&name=AGATECH&password=G1mnSB', ['http_errors' => false]);
//        $b = $response->getBody();
//        $ret = str_replace('ok:','',$b->getContents());
//        
//        $ret = json_decode($ret, true, 512, JSON_BIGINT_AS_STRING)["result"];
        $time = strtotime(date('Y-m-d'));
        $final = date("Y-m-d", strtotime("-30 day", $time));
//        $retC = file_get_contents('http://mylightning.ru/admin/m_request.php?reg=get&table=checks&win=1&windate:left='.$final.'&windate:right='.date('Y-m-d').'&ORDER=-windate&key='.$ret["key"].'&user='.$ret["id"]);
        
        // мега кастыль для того что бы брать победителей с сервера Опланет раскоментировать строки выше
        $retC = false; // а эту закоментировать
        
        if(!$retC){
            $MyTable = $this->findAll('winers',' WHERE windate > :windate',[ ':windate' => $final ]);
            $MyUArr=[];
            foreach ($MyTable as $key => $value) {
                $MyUArr[]=$value;
                
            }
            return $MyUArr;
        }
//        dd($retC);
        
        $retC = str_replace('ok:','',$retC);
        
        $retC = json_decode($retC, true, 512, JSON_BIGINT_AS_STRING)["result"];
        
        $UArr=[];
        foreach ($retC as $key => $value) {
            $retL = file_get_contents('http://mylightning.ru/admin/m_request.php?key=' . $ret["key"] . '&reg=get&table=users&id='.$value['parent_users'].'&user=' . $ret["id"]);
            $retL = str_replace('ok:','',$retL);
            $retL = json_decode($retL, true, 512, JSON_BIGINT_AS_STRING)["result"];
            $UArr[$key]=$retL;
            $UArr[$key]['winsum']=$value['winsum'];
            $UArr[$key]['fn']=$value['fn'];
            $UArr[$key]['windate']=date('d.m.Y',strtotime ($value['windate']));
            $UArr[$key]['jswindate']=date('Y.m.d',strtotime ($value['windate']));
            unset($UArr[$key]['id']);
            unset($UArr[$key]['email']);
            unset($UArr[$key]['password']);
            unset($UArr[$key]['role']);
            unset($UArr[$key]['party']);
            unset($UArr[$key]['key']);
            unset($UArr[$key]['disabled']);
            unset($UArr[$key]['gender']);
            unset($UArr[$key]['birthday']);
            unset($UArr[$key]['party_name']);
            
//            dd($value);
//            $this->wipe('winers');
//            $Table = $this->Dispense('winers');
//            $Table->name = $retL["name"];
//            $Table->phone = $retL["phone"];
//            $Table->winsum = $value["winsum"];
//            $Table->qr = $value["qr"];
//            $Table->fn = $value["fn"];
//            $Table->windate = date('Y-m-d',strtotime ($value['windate']));
//            $Table->jswindate = date('Y.m.d',strtotime ($value['windate']));
//            $Table->createdatetime = date('Y-m-d H:i:s');
//            $Table->fphone ='+7*****'.substr($UArr[$key]['phone'],-4);
//            $this->store($Table);

            $UArr[$key]['fphone']='+7*****'.substr($UArr[$key]['phone'],-4);
//           dd($value); 
        }
        
        
        return $UArr;
    }
    public function SetUserWinCheck($qr='') {
        $tempbean = $this->findOne($this->TableName,' qr = :qr', [ ':qr' => $qr ]);
        if($tempbean){
            $tempbean->winer = true;
            $tempbean->windate = date('Y-m-d H:i:s');
            return $this->store($tempbean);
        }
//        dd($tempbean);
        return false;
    }
    public function AddWiners($param) {
        $Table = $this->Dispense('winers');
        $Table->name = $param["name"];
        $Table->phone = $param["phone"];
        $Table->winsum = $param["winsum"];
        $Table->qr = $param["qr"];
        $this->SetUserWinCheck($param["qr"]);
        $Table->fn = $param["fn"];
        $Table->windate = date('Y-m-d',strtotime ($param['windate']));
        $Table->jswindate = date('Y.m.d',strtotime ($param['windate']));
        $Table->createdatetime = date('Y-m-d H:i:s');
        $Table->fphone ='+7*****'.substr($param['phone'],-4);
        $Table->wintext = $param['wintext'];
        $Table->winimg = $param['winimg'];
        return $this->store($Table);
    }
    public function SetUserTakedCheck($qr='') {
        $tempbean = $this->findOne($this->TableName,' qr = :qr', [ ':qr' => $qr ]);
        if($tempbean){
            $tempbean->taked = true;
            $tempbean->takeddate = date('Y-m-d H:i:s');
            return $this->store($tempbean);
        }
//        dd($tempbean);
        return false;
    }
    public function AddTakedChek($param) {
        $Table = $this->Dispense('takedchek');
        $Table->name = $param["name"];
        $Table->phone = $param["phone"];
        $Table->winsum = $param["winsum"];
        $Table->qr = $param["qr"];
        $this->SetUserTakedCheck($param["qr"]);
        $Table->fn = $param["fn"];
        $Table->givindate = date('Y-m-d',strtotime ($param['givindate']));
        $Table->jswindate = date('Y.m.d',strtotime ($param['windate']));
        $Table->createdatetime = date('Y-m-d H:i:s');
        //$Table->fphone ='+7*****'.substr($param['phone'],-4);
        return $this->store($Table);
    }
}
