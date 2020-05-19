<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of IndexController
 *
 * @author ivan kolotilkin
 */


class IndexController extends Controller {

    public function __construct() {
        parent::__construct();
//        $this->View->AddCss('/public/css/style.css');
        $this->View->AddCss('/public/css/style.min.css');
    }
    public function IndexAction() {
//        $MdlUser = new UserModel();
        $View = &$this->View;
        $View->title = 'Молния продаж';
//        $user=$MdlUser->GetCurrentUser();
//        dd($user);
//        if($user['role']>=200){
////            $View->VarSetArray($user);
//            $View->CurrentUser=json_encode($user);
//            $View->content = $View('page_clients.html');
//            return $View('index.html',TEMPLATE_DIR);
//        }
         $View->content = $View('index.html');
        return $View('index.html',TEMPLATE_DIR);
    }
    public function ClientAction() {
        $MdlUser = new UserModel();
        $View = &$this->View;
        $View->title = 'Молния продаж';
        $user=$MdlUser->GetCurrentUser();
//        dd($user);
        if($user['role']>=0){
//            $View->VarSetArray($user);
            $Config = new ConfigModel();
            $tmr = $Config->GetSetting('raffletime');
            $this->View->raffletime = $tmr;
            $View->CurrentUser=json_encode($user);
            $View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
            $View->content = $View('client.html');
            return $View('index.html',TEMPLATE_DIR);
        }
         $View->content = $View('index.html');
        return $View('index.html',TEMPLATE_DIR);
    }
    
    public function PartnersAction() {
        $MdlUser = new UserModel();
        $View = &$this->View;
        $View->title = 'Молния продаж';
        $user=$MdlUser->GetCurrentUser();
//        dd($user);
        $Config = new ConfigModel();
        $View->YaMapApiKey = $Config->GetSetting('YaMapApiKey');
        if($user['role']>=0){
//            $View->VarSetArray($user);
            $View->CurrentUser=json_encode($user);
            $View->content = $View('partner.html');
            return $View('index.html',TEMPLATE_DIR);
        }

         $View->content = $View('index.html');
        return $View('index.html',TEMPLATE_DIR);
    }
    public function FeedbackAction() {
        if (!$this->POST) return ['Error'=>"Only POST REQUEST"];
        
        $Config = new ConfigModel();
        
        $postdata=json_decode($this->REQUEST,true);
        
        $to = $Config->GetSetting('emailsend');//'komdir@agatech.ru';
        $subject = 'Сообщение с формы сайта. молнию продаж';
        $message = 'Новое сообщение от ФИО <b>'.$postdata['fio'].'</b> на сайте молния продаж.<br>Контактный телефон <b>'.$postdata['phone'].'</b><br>'. PHP_EOL;
        $message .= 'оставил свой маил адрес почты <b>' .$postdata['email'] .'</b> и написал вот такое сообщение '.PHP_EOL.PHP_EOL.'<h5>'.$postdata['message'].'</h5>';
        $headers = 'From: ' .$postdata['email'] . "\r\n" .
//                'Reply-To: admin@agatech.ru' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        $success = rr_mail($to, $subject, $message, $headers);
        if (!$success) {
            $success = error_get_last()['message'];
        }
        return ['SendStatus'=>$success,'SendTO'=>$to];
//        return $postdata;
    }
}
