<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of ApiController
 *
 * @author mvcrb
 */
class ApiController extends Controller{
    public $apiName = ''; //users
    protected $method = ''; //GET|POST|PUT|DELETE
    public $requestUri = [];
    public $requestParams = [];
    protected $action = ''; //Название метод для выполнения

    public function __construct() {
//        header("Access-Control-Allow-Orgin: *");
//        header("Access-Control-Allow-Methods: *");
//        header("Content-Type: application/json");

        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->requestParams = $_REQUEST;

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    public function IndexAction($method = null) {
        return ['Error' => "method not set"];
    }
    public function TestAction($method = null) {
        $this->View->content = $this->View->execute('test.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function SettingAction($func = null) {
        
        if (!$func) {
            return ['Error' => "method not set"];
        }
        $User = new UserModel();
        $UserVars = $User->GetCurrentUser();
        if ($UserVars['role'] < 500) {
            return ['Error' => "denide"];
        }
        $PostData = json_decode($this->REQUEST,true);
        switch ($func) {
            case 'setraffletime':
//                $raffDateTime = $PostData['datatime'];
                $raffDateTime = date("d.m.Y.h.i", strtotime($PostData['datatime']));
                $Config = new ConfigModel();
                $Config->SaveSetting('raffletime',$raffDateTime);
                return ['Success' => $raffDateTime];
            case 'setemailsend':
                $EmailSend = $PostData['EmailSend'];
                $Config = new ConfigModel();
                $Config->SaveSetting('emailsend',$EmailSend);
                return ['Success' => $EmailSend];
            case 'setyamapapi':
                $YaMapApiKey = $PostData['YaMapApiKey'];
                $Config = new ConfigModel();
                $Config->SaveSetting('YaMapApiKey',$YaMapApiKey);
                return ['Success' => $YaMapApiKey];
            case 'setmasteradmintoken':
                $MasterAdminToken = $PostData['MasterAdminToken'];
                $Config = new ConfigModel();
                $Config->SaveSetting('MasterAdminToken',$MasterAdminToken);
                return ['Success' => $MasterAdminToken];    
            default:
                return ['Error' => "Method '$func' not found"];
        }
    }
    public function LkAction($method=null) {
        if (!$method) return ['Error' => "API: Parameter not specified"];
//        if (!$this->POST) return ['Error'=>"Only POST"];
        $MethodName = mb_strtolower($method);
        switch ($MethodName) {
            case 'getallkkm': 
                $kkm = new KkmModel();
                return $kkm->GetAllKkm();
            default:
                return ['Error'=>"Method $method not found"];    
            }
        return ['Ok'=>$method];
    }
    public function UserAction($func = null) {
        if(!$func){
            return ['Error' => "method not set"];
        }
//        Session::init();
//        $var = Session::get('LoggedUser');
////        dd($var);
//        if ($var['role'] < 300) {
//            $User = new ClientModel();
//        } else {
//            $User = new UserModel();
//        }
        $User = new UserModel();
        $PostData = json_decode($this->REQUEST,true);
        switch ($func) {
            case 'GetAccessToken':
                if($User->login($PostData['email'], $PostData['password'])){
                    return ['AccessToken'=>$User->GenerateAccessToken($User->GetCurrentUser()['id'])];
                }
                return ['Error' => "Login or Password error"];
            case 'SetQrCodeString': 
                if($PostData){
                    
                    $UserId = $User->GetUserFromToken($PostData['AccessToken']);
                    if(is_null($UserId)){
                        return ['Error' => "User Access Token is wrong"];
                    }
                    $Check = new CheckModel();
                    $qrStatus = $Check->ValidateCheck($PostData['QrCodeString']);
                    if(isset($qrStatus['Success'])){
                        if($qrStatus['Success']==='is valid'){
                            $ret = $Check->AddUserCheck($UserId['id'], $PostData['QrCodeString']);
                            return $ret;
                        }
                    }else{
                        return $qrStatus;
//                        return ['Error' => "Check validate error",'FNS'=>$qrStatus];
                    }
//                    
//                    return ['$UserId' => $qrStatus];
                }
                return ['Error' => "Check data error"];
            default:
                return ['Error' => "Method '$func' not found"];
        }
//        return ['func'=>$func,
//            'REQUEST'=>json_decode($this->REQUEST),
//            'Method'=>$this->method
//            ];
    }
    
    public function ChecksAction($func = null) {
        if ($func) {
            $CollCom = mb_strtolower($func);
            $PostData = json_decode($this->REQUEST);
            $model = new CheckModel();
            switch ($CollCom) {
                case 'getlist':
                    return $model->wList($PostData);
                case 'getwiners':
                    return $model->GetWiners();
            }
            return $CollCom;
        }return [];
    }

    public function AddWinersAction($param=null) {
        $PostData = json_decode($this->REQUEST,true);
        $Config = new ConfigModel();
        $AccessToken = $Config->GetSetting('MasterAdminToken');//'37d2178f9790ba259beff4e04cbf2fab8f3a712877388c0f91c9c3cd28e6cfe8';
        if($PostData['AccessToken']!==$AccessToken){
            return ['Error' => "Wrong Access Token"];
        }
        $Chek = new CheckModel();
        return ['Success' => $Chek->AddWiners($PostData['data'])]; 
    }
    
    public function AddTakedChekAction($param=null) {
        $PostData = json_decode($this->REQUEST,true);
        $Config = new ConfigModel();
        $AccessToken = $Config->GetSetting('MasterAdminToken');// '37d2178f9790ba259beff4e04cbf2fab8f3a712877388c0f91c9c3cd28e6cfe8';
        if($PostData['AccessToken']!==$AccessToken){
            return ['Error' => "Wrong Access Token"];
        }
        $Chek = new CheckModel();
        return ['Success' => $Chek->AddTakedChek($PostData['data'])]; 
    }
}