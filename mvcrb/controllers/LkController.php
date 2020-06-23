<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of UserController
 *  
 * @author ivan kolotilkin
 * 
 * Внимание весь код без рефакторинга ибо некогда иэтим заниматся. так что читай и разбирайся сам!
 */
class LkController extends Controller {

    private $User;

    public function __construct() {
        parent::__construct();
        Session::init();
        $var = Session::get('LoggedUser');
//        dd($var);
        if($var['role'] < 300){
            $this->User = new ClientModel();
        }else{
            $this->User = new UserModel();
        }
//        $this->User = new UserModel();
        if ($this->User->GetCurrentUser()['role'] < 100) {
            if ($this->POST) {
                if (!headers_sent()) {
                    header("HTTP/1.1 403 Forbidden");
                    header("Status: 403 Forbidden");
                }
                die('Forbidden: Asses denide');
            } else {
                Session::set('UserRedirect', mvcrb::$URI);
                return mvcrb::Redirect('/user/login');
            }
        }
        $curUser = $this->User->GetCurrentUser();
        if ($curUser['role'] == 100) {
            $this->View->title = 'Личный кабинет участника проекта';
            $this->View->MnuCssClass = 'clients-navigation';
            $this->View->ContentCssClass = 'clients-content-bgc';
        } elseif ($curUser['role'] == 300) {
            $this->View->title = 'Личный кабинет партнера';
            $this->View->MnuCssClass = 'partners-navigation';
            $this->View->ContentCssClass = 'partners-content-bgc';
        } elseif ($curUser['role'] == 500) {
            $this->View->title = 'Модератор';
            $this->View->MnuCssClass = 'partners-navigation';
            $this->View->ContentCssClass = 'clients-content-bgc';
        } elseif ($curUser['role'] >= 900) {
            $this->View->title = 'Администратор';
            $this->View->MnuCssClass = 'partners-navigation';
            $this->View->ContentCssClass = 'clients-content-bgc';
        }
        $this->View->username = $curUser['firstname'] .' '.$curUser['lastname'];
        $this->View->userphone = $curUser['phone'];
        $this->View->useremail = $curUser['email'];
        $this->View->userrole=$curUser['role'];
        $cmodel = new CheckModel();
        $this->View->checkcount = ($curUser['kountchek']-$cmodel->CheckCount($curUser['id']));
//        dd($this->View->checkcount);
        $this->View->AddCss('/public/css/style_personalAccount.css');
        $this->View->AddCss('/public/css/style.min.css');
    }
    public function DocsAction() {
        $this->View->admincontent = $this->View->execute('docs.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function UserAction() {
        $curUser = $this->User->GetCurrentUser();
        if ($curUser['role'] == 100) {
            $this->View->admincontent = $this->View->execute('client.html');
        }else{
            $this->View->admincontent = $this->View->execute('pclient.html');
        }
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function KKmAction() {
        $Config = new ConfigModel();
        $this->View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
        $this->View->admincontent = $this->View->execute('partner_kkm.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function PartnerAction() {
        $this->View->admincontent = $this->View->execute('partner.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ModeratorclientAction() {
        $curUser = $this->User->GetCurrentUser();
        if ($curUser['role'] < 500) {
            $this->View->admincontent = 'Доступ запрещен';
            $this->View->content = $this->View->execute('AdminWraper.html');
            return $this->View->execute('index.html', TEMPLATE_DIR);
        }
        $this->View->admincontent = $this->View->execute('moderatorclient.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ModeratorAction() {
        $curUser = $this->User->GetCurrentUser();
        if ($curUser['role'] < 500) {
            $this->View->admincontent = 'Доступ запрещен';
            $this->View->content = $this->View->execute('AdminWraper.html');
            return $this->View->execute('index.html', TEMPLATE_DIR);
        }
        $this->View->admincontent = $this->View->execute('moderator.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ChecksAction() {
        $this->View->admincontent = $this->View->execute('checks.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function CheckspartnerAction() {
        $this->View->admincontent = $this->View->execute('partner_checks.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function MapAction() {
        $Config = new ConfigModel();
        $this->View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
        $this->View->admincontent = $this->View->execute('map.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }    
    public function StatisticAction() {

        $this->View->admincontent = $this->View->execute('statistic.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    } 
    
    public function ClientcheckAction() {

        $this->View->admincontent = $this->View->execute('clientchecks.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ClientlkAction() {
        Session::init();
        $curUser = $this->User->GetCurrentUser();
        $curUser['fakerole']=!$curUser['fakerole'];
        Session::set('LoggedUser', $curUser);
        
        $curUser = $this->User->GetCurrentUser();
        
//        dd($curUser);
//        $this->View->admincontent = $this->View->execute('statistic.html');
//        $this->View->content = $this->View->execute('AdminWraper.html');
//        return $this->View->execute('index.html', TEMPLATE_DIR);
        return mvcrb::Redirect('/lk');
    }
    public function IndexAction() {
        $curUser = $this->User->GetCurrentUser();
//        dd($this->View);
        if($curUser['role']==100){
            $this->View->admincontent = $this->View->execute('statistic.html');
        }elseif($curUser['role']==300){
            $this->View->admincontent = $this->View->execute('partner.html');
        }elseif($curUser['role']==500){
            $Config = new ConfigModel();
            $tmr = $Config->GetSetting('raffletime');
            $this->View->raffletime = date("Y-m-d\Th:i", strtotime($tmr));
            $this->View->EmailSend = $Config->GetSetting('emailsend');
            $this->View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
            $this->View->MasterAdminToken = $Config->GetSetting('MasterAdminToken');
            $this->View->admincontent = $this->View->execute('root.html');
        }elseif($curUser['role']>=900){
            $Config = new ConfigModel();
            $tmr = $Config->GetSetting('raffletime');
            $this->View->raffletime = date("Y-m-d\Th:i", strtotime($tmr));
            $this->View->EmailSend = $Config->GetSetting('emailsend');
            $this->View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
            $this->View->MasterAdminToken = $Config->GetSetting('MasterAdminToken');
            $this->View->admincontent = $this->View->execute('root.html');
        }

        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function HelpsAction() {
        $this->View->admincontent = $this->View->execute('helps.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function QrkodrederAction() {
        $curUser = $this->User->GetCurrentUser();
        //if($curUser['token']){
            $curUser['token']=$this->User->GenerateAccessToken($curUser['id']);
        //}
        $this->View->token = $curUser['token'];
        
        $this->View->admincontent = $this->View->execute('qrider.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }
    public function ApiAction($method=null) {
        if (!$method) return ['Error' => "API: Parameter not specified"];
//        if (!$this->POST) return ['Error'=>"Only POST"];
        $MethodName = mb_strtolower($method);
        switch ($MethodName) {
            case 'getallkkm': 
                $kkm = new KkmModel();
                return $kkm->GetAllKkm();
            default:
                return ['Error'=>"Method $Method not found"];    
            }
        return ['Ok'=>$method];
    }
    public function GetmenuAction() {
        $curUser = $this->User->GetCurrentUser();
        if ($curUser['role'] == 100) {
            $Data = [
                ['id' => '1', 'parent' => '0', 'name' => 'СТАТИСТИКА РОЗЫГРЫШЕЙ', 'src' => '/lk/statistic', 'class' => 'far fa-file'],
                ['id' => '3', 'parent' => '0', 'name' => 'КАРТОЧКА УЧАСТНИКА', 'src' => '/lk/user', 'class' => 'fas fa-home'],
                ['id' => '6', 'parent' => '0', 'name' => 'Партнёры на карте', 'src' => '/lk/map', 'class' => 'fas fa-calculator'],
                ['id' => '7', 'parent' => '0', 'name' => 'Правила участия', 'src' => '/lk/helps', 'class' => 'fas fa-calculator'],
                ['id' => '8', 'parent' => '0', 'name' => 'Ваши чеки', 'src' => '/lk/clientcheck', 'class' => 'fas fa-calculator'],
           ];
            return $Data;
        } elseif ($curUser['role'] == 300) {
            $Data = [
                ['id' => '1', 'parent' => '0', 'name' => 'Данные', 'src' => '/lk/user', 'class' => 'fas fa-home'],
                ['id' => '2', 'parent' => '0', 'name' => 'Карточка партнёра', 'src' => '/lk/', 'class' => 'fas fa-users-cog'],
                ['id' => '3', 'parent' => '0', 'name' => 'Точки продаж и ККМ', 'src' => '/lk/kkm', 'class' => 'far fa-file'],
                ['id' => '4', 'parent' => '0', 'name' => 'Статистика по чекам', 'src' => '/lk/checkspartner', 'class' => 'fas fa-home'],
                ['id' => '4', 'parent' => '0', 'name' => 'Карта партнёров', 'src' => '/lk/map', 'class' => 'far fa-file'],
//                ['id' => '5', 'parent' => '0', 'name' => 'СТАТИСТИКА ПО ЧЕКАМ', 'src' => '/lk/chekstat', 'class' => 'fas fa-calculator'],
                ['id' => '6', 'parent' => '0', 'name' => 'ПОБЕДИТЕЛИ', 'src' => '/lk/statistic', 'class' => 'fas fa-calculator'],
//                ['id' => '7', 'parent' => '0', 'name' => 'ПАКЕТЫ ЧЕКОВ', 'src' => '/lk/cheks', 'class' => 'fas fa-calculator'],
//                ['id' => '8', 'parent' => '0', 'name' => 'АКЦИИ', 'src' => '/lk/akci', 'class' => 'fas fa-calculator'],
//                ['id' => '9', 'parent' => '0', 'name' => 'УЧАСТНИКИ','src'=>'/lk/users','class'=>'fas fa-calculator'],
//                ['id' => '10', 'parent' => '0', 'name' => 'КАРТА ПАРТНЕРОВ','src'=>'/lk/map','class'=>'fas fa-calculator']
            ];
            if($curUser['fakerole']){
//            $fData = [                
//                
//                ];
            $Data= [['id' => '1', 'parent' => '0', 'name' => 'СТАТИСТИКА РОЗЫГРЫШЕЙ', 'src' => '/lk/statistic', 'class' => 'far fa-file'],
                ['id' => '3', 'parent' => '0', 'name' => 'КАРТОЧКА УЧАСТНИКА', 'src' => '/lk/user', 'class' => 'fas fa-home'],
                ['id' => '6', 'parent' => '0', 'name' => 'Партнёры на карте', 'src' => '/lk/map', 'class' => 'fas fa-calculator'],
                ['id' => '7', 'parent' => '0', 'name' => 'Правила участия', 'src' => '/lk/helps', 'class' => 'fas fa-calculator']];
            }
            
            return $Data;
        } elseif ($curUser['role'] == 500) {
            $Data = [
                ['id' => '0', 'parent' => '0', 'name' => 'Настройки', 'src' => '/lk/', 'class' => 'fas fa-home'],
                ['id' => '1', 'parent' => '0', 'name' => 'Управление партнерами', 'src' => '/lk/moderator', 'class' => 'fas fa-home'],
//                ['id' => '2', 'parent' => '0', 'name' => 'Управление клиентами', 'src' => '/lk/moderatorclient', 'class' => 'fas fa-home'],
                ['id' => '6', 'parent' => '0', 'name' => 'Карта партнёров', 'src' => '/lk/map', 'class' => 'fas fa-calculator'],
            ];
            return $Data;
        } elseif ($curUser['role'] >= 900) {
            $Data = [
                ['id' => '0', 'parent' => '0', 'name' => 'Настройки', 'src' => '/lk/', 'class' => 'fas fa-home'],
                ['id' => '1', 'parent' => '0', 'name' => 'Пользователь', 'src' => '/lk/user', 'class' => 'fas fa-home'],
                ['id' => '2', 'parent' => '0', 'name' => 'Партнер', 'src' => '/lk/partner', 'class' => 'fas fa-home'],
                ['id' => '3', 'parent' => '0', 'name' => 'Партнер ККМ', 'src' => '/lk/kkm', 'class' => 'fas fa-home'],
                ['id' => '4', 'parent' => '0', 'name' => 'Управление партнерами', 'src' => '/lk/moderator', 'class' => 'fas fa-home'],
                ['id' => '4', 'parent' => '0', 'name' => 'Управление клиентами', 'src' => '/lk/moderatorclient', 'class' => 'fas fa-home'],
                ['id' => '5', 'parent' => '0', 'name' => 'Чеки', 'src' => '/lk/checkspartner', 'class' => 'fas fa-home'],
                ['id' => '6', 'parent' => '0', 'name' => 'Карта партнёров', 'src' => '/lk/map', 'class' => 'fas fa-calculator'],
                ['id' => '7', 'parent' => '0', 'name' => 'Сканер кода', 'src' => '/lk/qrkodreder', 'class' => 'fas fa-calculator'],
                ['id' => '8', 'parent' => '0', 'name' => 'Победители', 'src' => '/lk/statistic', 'class' => 'far fa-file']
            ];
            return $Data;
        }

    }

}
