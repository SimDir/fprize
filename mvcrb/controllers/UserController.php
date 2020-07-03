<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of UserController
 *  
 * @author ivan kolotilkin
 */

use GuzzleHttp\Client;

class UserController extends Controller {

    private $User;

    public function __construct() {
        parent::__construct();
        $this->User = new UserModel();//$this->GetModel('User'); 
        $this->View->AddCss('/public/css/style.css');

//        dd(apacher_request_headers());
//        $this->View->SetWivePath(TEMPLATE_DIR.'UserController'.DS);
    }

    public function IndexAction($id=null) {
        Session::init();
        $UserVars = $this->User->GetCurrentUser();
        if ($UserVars['role'] == 0) {
            return mvcrb::Redirect('/user/login/');
        }
        $redir = Session::get('UserRedirect');
        if ($redir) {
            Session::set('UserRedirect', null);
            return mvcrb::Redirect($redir);
        }
        return mvcrb::Redirect('/lk/');

    }

    public function LogoutAction() {
        Session::destroy();
        return mvcrb::Redirect('/');
    }

    public function LoginAction() {
        if ($this->User->GetCurrentUser()['role'] >= 100) {
            return mvcrb::Redirect('/lk/');
        }
        $this->View->title = 'Вход пользователя';
        if ($this->POST) {
            $user = json_decode($this->REQUEST);
            return $this->User->login($user->email, $user->password);
        }
        $this->View->state = 'Login';
        $this->View->content = $this->View->execute('UserForms.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function RegistreAction($usertype='client') {
        if ($this->User->GetCurrentUser()['role'] > 0) {
            return mvcrb::Redirect('/lk/');
        }
        if ($this->POST) {
            $headers = 'From: admin@agatech.ru' . "\r\n" .
//                        'Reply-To: support@agatech.ru' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
            
            $PostUserData = json_decode($this->REQUEST,true);
            if($this->User->ChekUserPhone($PostUserData['phone'])){
                return ['Error'=>'Телефон уже используется'];
            }
            if($this->User->ChekMail($PostUserData['email'])){
                return ['Error'=>'email уже занят'];
            }
            
            if ($PostUserData['usertype']=='partner') {
                $message = 'Зарегестрировался новый партнер с маил адресом ' . $PostUserData['email'] . PHP_EOL;
                $PostUserData['role']=300;
            } else {
                $message = 'Зарегестрировался новый участник с маил адресом ' . $PostUserData['email'] . PHP_EOL;
                $PostUserData['role']=100;
            }
            $success=$this->User->CreateFullUser($PostUserData);
            $this->User->login($PostUserData['email'], $PostUserData['password']);
            if($success){
                rr_mail($PostUserData['email'], 'Регистрация на сервисе Призовая молния', "Вы успешно зарегестрированны в сервисе \"Призовая молния\"", $headers);
            }
            $Config = new ConfigModel();

            $to = $Config->GetSetting('emailsend'); //'komdir@agatech.ru';
            $subject = 'Сообщение для модератора. Новый пользователь на сайте';
            

            $success = rr_mail($to, $subject, $message, $headers);
            if (!$success) {
                $success = error_get_last()['message'];
            }
            return ['Success'=>$success];
        }
        $this->View->title = 'Регистрация пользователя';
        $this->View->state = 'Registre';
        $this->View->usertype = $usertype;
        $this->View->content = $this->View->execute('UserForms.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function GetAction() {
        if ($this->POST)
            return $this->User->GetCurrentUser();
        return mvcrb::Redirect('/user');
    }
    public function SetpasswordAction() {
         $DataUser = json_decode($this->REQUEST,true);
         $fUser = $this->User->GetUserFromCode($DataUser['code']);
         if($fUser){
//            mvcrb::Redirect('/lk/');
             $this->User->PasswordCodeEmailDele($fUser['id']);
            return ['success'=>$this->User->ChangePassword($fUser['id'],$DataUser['password'])];
         }
         return ['error'=>"пользователь не найден"];
    }
    public function PaswdAction($UniqId) {
        $fUser = $this->User->GetUserFromCode($UniqId);
        if ($fUser) {
            $this->View->code = $UniqId;
            $this->View->userid = $fUser['id'];
            $this->View->content = $this->View->execute('codepassword.html');
            return $this->View->execute('index.html', TEMPLATE_DIR);
        }
//        $this->View->content = $this->View->execute('nofinduser.html');
        return mvcrb::Redirect(ERROR_URL);//$this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ForgotpasswordAction() {
        if ($this->POST) {
            $email = json_decode($this->REQUEST,true)['email'];
            if ($this->User->ChekMail($email)) {
                $User = $this->User->GetUserFromEmail($email);
                $UniqId =  $this->User->UniqIdReal();
                $subject = 'Восстановление пароля';
                $message = 'Вы нажали восстановить пароль' . PHP_EOL;
                $message .= "Что-бы восстановить ваш пароль пройдите по ссылке  https://fprize.ru/user/paswd/$UniqId" . PHP_EOL;
                $message .= "или нажмите   <a href=\"https://fprize.ru/user/paswd/$UniqId\">Сюда</a> Что-бы восстановить ваш пароль" . PHP_EOL;
                $headers = 'From: admin@agatech.ru' . "\r\n" .
//                        'Reply-To: support@agatech.ru' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                $ret =  rr_mail($email, $subject, $message, $headers);
                if($ret){
                    $this->User->PasswordCodeEmailReset($User['id'], $UniqId);
                    return ['success'=>$ret];
                }
                return ['error'=>'Ошибка сервера во время отправки пароля, пароль не изменен. обратитесь к администрации'];
            }
            return ['error'=>"пользователь с $email не найден"];
        }
        $this->View->content = $this->View->execute('forgotpassword.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ApitestAction($param) {
        $User = $this->User;
        $SendUserId = $User->GetUserID((int)$param);
        $uKKm = new KkmModel();
        $SendUserId['kkmdata']=$uKKm->getUserKkm($SendUserId['id']);
        return $this->sendOplanet($SendUserId);
    }
    private function sendOplanet($partnerData) {
        $client = new Client();
        $response = $client->request('GET',
                'http://mylightning.ru/admin/m_request.php?reg=login&name=agatech_api&password=p8hpxf', ['http_errors' => false]);
        $b = $response->getBody();
        $ret = str_replace('ok:','',$b->getContents());
        $ret = json_decode($ret, true, 512, JSON_BIGINT_AS_STRING)["result"];
        $jsondata = json_encode($partnerData,JSON_UNESCAPED_UNICODE);
//        return 'strjs = '.$jsondata;
        $responseKey = $client->request('GET',
                'http://mylightning.ru/admin/m_request.php?user='.$ret['id'].'&key='.$ret['key'].'&reg=addpartie&data='.$jsondata, ['http_errors' => false]);
        
        $bk = $responseKey->getBody();
//        http://mylightning.ru/admin/m_request.php?user=4285&key=2243&reg=addpartie&data=<json>
        return $bk->getContents();
    }
    public function ApiAction($Method=null) {
        if(!$Method) return ['Error'=>"API: Parameter not specified"];
//        if (!$this->POST) return ['Error'=>"Only POST"];
        $MethodName = mb_strtolower($Method);
        $User = $this->User;
        switch ($MethodName) {
            case 'get': 
                return $User->GetCurrentUser();
            case 'save': 
                $curUserId = $User->ChangeDataUser($User->GetCurrentUser()['id'],json_decode($this->REQUEST,true));
                return ['success'=>$curUserId];
            case 'getpartner': 
                $cu = $User->GetCurrentUser();
                if ($cu['role'] < 500) {
                    return ['error' => 'access denied for user'];
                }
                return $User->GetPartnerList(json_decode($this->REQUEST));
            case 'sendmailbyparner': 
                $cu = $User->GetCurrentUser();
                if ($cu['role'] < 500) {
                    return ['error' => 'access denied for user'];
                }
                $SendUserId = $User->GetUserID(json_decode($this->REQUEST,true)['UserID']);
                
                $Config = new ConfigModel();

                $to = $Config->GetSetting('emailsend'); //'komdir@agatech.ru';
                $subject = 'Сообщение для модератора. молния продаж';

                $message = 'Email <b>' . $SendUserId['email'] .'</b><br>'. PHP_EOL;
                $message .= 'Телефон <b>' . $SendUserId['phone'] .'</b><br>'. PHP_EOL;
                $message .= 'ИНН <b>' . $SendUserId['inn'] .'</b><br>'. PHP_EOL;
                $message .= 'Фамилия <b>' . $SendUserId['surname'] .'</b><br>'. PHP_EOL;
                $message .= 'Имя <b>' . $SendUserId['firstname'] .'</b><br>'. PHP_EOL;
                $message .= 'Отчество <b>' . $SendUserId['lastname'] .'</b><br>'. PHP_EOL;
                $message .= 'Наименование организации <b>' . $SendUserId['kompany'] .'</b><br>'. PHP_EOL;
                $message .= 'Юридический адрес <b>' . $SendUserId['juraddress'] .'</b><br>'. PHP_EOL;
                $message .= 'Почтовый одрес <b>' . $SendUserId['postaddress'] .'</b><br>'. PHP_EOL;
                $message .= 'ОГРН <b>' . $SendUserId['ogrn'] .'</b><br>'. PHP_EOL;
                $message .= 'Счет <b>' . $SendUserId['schet'] .'</b><br>'. PHP_EOL;
                $message .= 'Коренспонденский счет <b>' . $SendUserId['corschet'] .'</b><br>'. PHP_EOL;
                $message .= 'БИК <b>' . $SendUserId['bik'] .'</b><br>'. PHP_EOL;
                $message .= 'Название банка <b>' . $SendUserId['bankname'] .'</b><br>'. PHP_EOL;
                $message .= 'ФИО Директора <b>' . $SendUserId['bossname'] .'</b><br>'. PHP_EOL;
                $message .= 'Тип руководителя <b>' . $SendUserId['bosstype'] .'</b><br>'. PHP_EOL;
                $message .= 'КПП <b>' . $SendUserId['kpp'] .'</b><br>'. PHP_EOL;
                $message .= 'Статус <b>' . $SendUserId['status'] .'</b><br>'. PHP_EOL;
                
                $headers = 'From: robot@fprize.ru' . "\r\n" .
//                        'Reply-To: admin@agatech.ru' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                $success = rr_mail($to, $subject, $message, $headers);
                if (!$success) {
                    $success = error_get_last()['message'];
                }
//                file_put_contents(SITE_DIR.'test.json', json_encode($SendUserId));
//                $this->sendOplanet($SendUserId);
                $uKKm = new KkmModel();
                $SendUserId['kkmdata']=$uKKm->getUserKkm($SendUserId['id']);
                return ['SendStatus' => $success, 'SendTO' => $to,'perterdataOplanet' => $this->sendOplanet($SendUserId)];
//                return $message;    
            case 'savepartner': 
                $cu = $User->GetCurrentUser();
                if($cu['role']<500){
                    return ['error'=>'access denied for user'];
                }
                $userId = json_decode($this->REQUEST,true)['id'];
                $partnerData = json_decode($this->REQUEST,true);
                unset($partnerData['id']);
                $partnerId = $User->ChangeDataUser($userId,$partnerData);
                $SendUserId = $User->GetUserID($userId);
                $uKKm = new KkmModel();
                $SendUserId['kkmdata']=$uKKm->getUserKkm($SendUserId['id']);
                $this->sendOplanet($SendUserId);
                return ['success'=>$partnerId];
            case 'addkkm':
                $cu = $User->GetCurrentUser();
                $kkm = new KkmModel();
                return $kkm->Add(json_decode($this->REQUEST,true),$cu['id']);
            case 'getkkm': 
                $cu = $User->GetCurrentUser();
                $kkm = new KkmModel();
                return $kkm->getUserKkm($cu['id']);
            case 'savekkm':
                $data = json_decode($this->REQUEST, true);
                $id = $data['id'];
                unset($data['id']);
                unset($data['user_id']);
                $kkm = new KkmModel();
                return $kkm->Edit($data, $id);
            case 'delkkm':
                $cu = $User->GetCurrentUser();
                $data = json_decode($this->REQUEST, true);
                $id = (int)$data['kkmid'];
                $kkm = new KkmModel();
                // проверку на собственные ID шники что бы не могли удалить свои...
                $uKkml = $kkm->getUserKkm($cu['id']);
                $key = array_search($id, array_column($uKkml, 'id'));
                if($key===FALSE){
                    return ['error'=>'You KKM not found'];
                }
                return ['success'=>'Ok del KKM id='.$kkm->Del($id)];
            case 'getcheck':
                $model = new CheckModel();
                $PostData = json_decode($this->REQUEST);
                $cu = $User->GetCurrentUser();
                return $model->uList($PostData,$cu['id']);
            case 'getcheckcount':   
                $cu = $User->GetCurrentUser();
                $model = new CheckModel();
                return $model->CheckCount($cu['id']);
            default:
                return ['Error'=>"Method $Method not found"];    
        }
//        return $MethodName;
    }

}
