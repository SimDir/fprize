<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');
/**
 * Description of FnsController
 *
 * @author Ivan P Kolotilkin
 */

use GuzzleHttp\Client;

class FnsController extends Controller{
        public function __construct() {
        parent::__construct();
        $this->User = new UserModel();
        if ($this->User->GetCurrentUser()['role'] < 100) {
            if ($this->POST) {
                if (!headers_sent()) {
                    header("HTTP/1.1 403 Forbidden");
                    header("Status: 403 Forbidden");
                }
                die('Forbidden: Asses denide');
            } else {
                Session::set('UserRedirect', mvcrb::$URI);
                return mvcrb::Redirect('/user');
            }
        }
        $this->View->title = 'Личиночный кабинет';
    }

//    public function IndexAction() {
//        $this->View->title = 'Молния продаж - личный кабинет партнера';
//        $this->View->content = $this->View->execute('index.html');
//        return $this->View->execute('index.html', TEMPLATE_DIR);
//    }
    public function QrAction() {
        $this->View->title = 'Молния продаж - QR код сканер';
        $this->View->content = $this->View->execute('qrider.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function GetAction($qrs = null) {

        
        
//        $response = $client->request('POST', 
//                'https://proverkacheka.nalog.ru:9999/v1/mobile/users/signup', 
//                ['json' =>['email' => 'stariktz@mail.ru','name' => 'StarikTZ','phone' => '+79021290036']]);
        
//        $response = $client->request('GET', 
//                'https://proverkacheka.nalog.ru:9999/v1/mobile/users/login');
//        $FN = '9286000100071678'; //<номер ФН>
//        $FD = '90633'; //<номер ФД>
//        $FP = '2272512458'; //<номер ФДП>
//        $VFc = '1'; //<вид кассового чека
//        $date = '20200121'; // Дата
//        $time = '122600'; // Время
//        $sum = '42.50'; // Сумма
//        $response = $client->request('GET',
//                'https://proverkacheka.nalog.ru:9999/v1/ofds/*/inns/*/fss/' . $FN . '/operations/' . $VFc . '/tickets/' . $FD . '?fiscalSign=' . $FP . '&date=' . $date . 'T' . $time . '&sum=' . $sum, ['http_errors' => false]);

        $t ='20200122';// Дата
        $T = '2057';// Время
        $s = '83950';// Сумма
        $fn = '9282440300479231';//<номер ФН>
        $i = '372';//<номер ФД>
        $fp = '205537033';//<номер ФДП>
        $n = '1';//<вид кассового чека
        $qr=$qrs;
        if($qrs){
//            $qr=$qrs;
            $requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $requestURI = str_replace('/fns/get/code?', '', $requestURI);
            $qr=$requestURI;
//            dd($requestURI);
        }else{
            return ['error' => 'no data',
//                'HeaderLine'=>$response->getHeaderLine('Content-Type'),
                'StatusCode' => 504
            ];
            //$qr='t=20200122T180800&s=500.00&fn=9280440300646561&i=23658&fp=854673406&n=1';
        }
        //$chekMDL = new CheckModel();
        //return $chekMDL->ValidateCheck($qr);
        // 5f5f35450b851a7cd26086ec 5e2a8a510fd2caa326aebc7d
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
        return ['aa'=>$fnsRet,'bb'=>$url];
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
        
//        $sessionId = $b->getContents();
        $sessionId = json_decode($b->getContents(), true)['sessionId'];
        $headers['sessionId']=$sessionId;
//        dd($sessionId['sessionId']);
        
        $jsonQr=['qr'=>$qr];
        $response = $client->request('POST', 
            'https://irkkt-mobile.nalog.ru:8888/v2/ticket', ['json'=>$jsonQr,'headers' => $headers,'http_errors' => false]);
        $b = $response->getBody();
        
        $ct = $b->getContents();
        $status = json_decode($ct, true)['status'];
//        if($status==2){
//            return ['Success' => 'is valid'];
//        }else{
//            return ['Error FNS Server' => $ct,
//                'StatusCode' => $response->getStatusCode()
//            ];
//        
//        }
        $response = $client->request('GET', 
            'https://irkkt-mobile.nalog.ru:8888/v2/tickets/'.$ticket_id, ['json'=>$jsonQr,'headers' => $headers,'http_errors' => false]);
        $b = $response->getBody();
        return $b->getContents();

//        return ['error'=>$b->getContents(),
//                
//                'StatusCode'=>$response->getStatusCode()
//                ];
    }
    
    public function ChecksAction() {
        $this->View->title = 'Молния продаж - личный кабинет админа';
        $this->View->content = $this->View->execute('Checks.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    
    public function ApiAction($param=null) {
        if ($param) {
            $CollCom = mb_strtolower($param);
            $PostData = json_decode($this->REQUEST);
            $model = $this->GetModel('check');
            switch ($CollCom) {
                case 'getcheckslist':
                    return $model->List($PostData);
                    
            }
            return $CollCom;
        }return [];
    }
    public function TestAction($qr='') {
//        $qr='t=20200122T180800&s=500.00&fn=9280440300646561&i=23658&fp=854673406&n=1';
        if($qr){
//            $qr=$qrs;
            $requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $requestURI = str_replace('/fns/test/', '', $requestURI);
            $qr=$requestURI;
//            dd($requestURI);
        } else {
            $qr='t=20200122T180800&s=500.00&fn=9280440300646561&i=23658&fp=854673406&n=1';
        }
        $cm = new CheckModel();
        return $cm->ValidateCheck($qr);
    }
}
