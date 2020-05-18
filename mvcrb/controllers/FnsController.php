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
        $headers = ['Device-Id'=>'1488', 'Device-OS'=>'rusbeard mvc php'];
        $client = new Client([
            'auth' => ['+79021290036', '588807'],
            'headers' =>$headers
        ]);
        
        
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
        if($qrs){
//            $qr=$qrs;
            $requestURI = filter_input(INPUT_SERVER, 'REQUEST_URI');
//            $requestURI = str_replace('/fns/get/code?', '', $requestURI);
            $qr=$requestURI;
//            dd($requestURI);
        }else{
            return ['error' => 'no data',
//                'HeaderLine'=>$response->getHeaderLine('Content-Type'),
                'StatusCode' => 504
            ];
            //$qr='t=20200122T180800&s=500.00&fn=9280440300646561&i=23658&fp=854673406&n=1';
        }
        preg_match("/t=(\w+)T/", $qr, $t);
        preg_match("/T(\w+)/", $qr, $T);
        preg_match("/s=(\w+.\w+)/", $qr, $s);
        $s = str_replace('.', '', $s);
        preg_match("/fn=(\w+)/", $qr, $fn);
        preg_match("/i=(\w+)/", $qr, $i);
        preg_match("/fp=(\w+)/", $qr, $fp);
        preg_match("/&n=(\w+)/", $qr, $n);
//        dd($n);    
        $t =$t[1];// Дата
        $T = $T[1];// Время
        $s = $s[1];// Сумма
        $fn = $fn[1];//<номер ФН>
        $i = $i[1];//<номер ФД>
        $fp = $fp[1];//<номер ФДП>
        $n = $n[1];//<вид кассового чека   
            
        $response = $client->request('GET', 
            'https://proverkacheka.nalog.ru:9999/v1/ofds/*/inns/*/fss/'.$fn.'/operations/'.$n.'/tickets/'.$i.'?fiscalSign='.$fp.'&date='.$t.'T'.$T.'&sum='.$s, ['http_errors' => false]); 
       
//        $response = $client->request('GET',
//                'https://proverkacheka.nalog.ru:9999/v1/inns/*/kkts/*/fss/'.$fn.'/tickets/'.$i.'?fiscalSign='.$fp.'&sendToEmail=no', ['http_errors' => false]);
        
        $b = $response->getBody();
//        return $b->getContents();
        if($response->getStatusCode()==204){
            return ['success'=> 'is valid'];
        }
        return ['error'=>$b->getContents(),
//                'HeaderLine'=>$response->getHeaderLine('Content-Type'),
                'StatusCode'=>$response->getStatusCode()
                ];
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
}
