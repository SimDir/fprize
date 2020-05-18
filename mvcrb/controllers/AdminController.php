<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of UserController
 *  
 * @author ivan kolotilkin
 */
class AdminController extends Controller {

    private $User;

    public function __construct() {
        parent::__construct();
        $this->User = new UserModel();
//        dd($this->User->GetCurrentUser());
        if ($this->User->GetCurrentUser()['role'] < 900) {

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
        $this->View->AddCss('/public/css/admin/adminstyle.css');
        $this->View->title = 'Админка';
    }

    public function UpdateAction($param = null) {        
        $directory = new \RecursiveDirectoryIterator(mvcrb::Config()['App_Controllers_Dir']);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = [];
        foreach ($iterator as $info) {
            if (is_file($info->getPathname())) {
                $files[] = basename($info->getPathname(),'Controller.php');
            }

        }
//        dd($files);

        return $files;
    }

    public function IndexAction() {
        
        $this->View->admincontent = $this->View->execute('main.html');
        if($this->POST){
            return ['Content'=>$this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function EditorAction($Editor = 'tinymce', $id = 0) {
        $Page = new PageModel();
        $p = $Page->GetPage($id);
        $this->View->apikey='2vf7bxyhwhgjglrd05fapr353yk2xhegpnqdxgnhdk78rsie';
        $this->View->EditorText = $p['content'];
        $this->View->id = $p['id'];
        $this->View->content = $this->View->execute('Editor.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function UserAction($param=false) {
        if($param){
            $CollCom = mb_strtolower($param);
            $PostData = json_decode($this->REQUEST);
            switch ($CollCom) {
                case 'delete':
                    $UserID = json_decode($this->REQUEST,true)['UserID'];
                    return ['success' => true, 'id' =>$this->User->DellUser($UserID)];
                    break;
                case 'edit':
                    return ['success' => true, 'id' => $this->User->EditUser($PostData->email, isset($PostData->password) ? $PostData->password : '', $PostData->login, $PostData->role, $PostData->firstname, $PostData->lastname, $PostData->phone, $PostData->id)];
                    break;
                case 'add':
                    if ($this->User->ChekMail($PostData->email)) {
                        $errors[] = 'Пользователь с таким Email уже существует!';
                    }
                    if ($this->User->ChekUserLogin($PostData->login)) {
                        $errors[] = 'Пользователь с таким логином уже существует!';
                    }
                    if (isset($errors)) {
                        return ['Errors' => $errors];
                    }
                    return ['success' => true, 'id' => $this->User->CreateUser($PostData->email, $PostData->password, $PostData->login, $PostData->role, $PostData->firstname, $PostData->lastname, $PostData->phone)];
                    break;
                case 'list':
                    return $this->User->GetList(json_decode($this->REQUEST));
                    break;
                case 'getexel':
                    return $this->User->GetExel();
                    break;
                case 'export':
                    $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

                    if(in_array($_FILES["file"]["type"], $allowedFileType)){

                        $targetPath = SITE_DIR.'public'.DS. 'uploads'.DS.$_FILES['file']['name'];
                        
                        if(move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)){
                            return $this->User->SetExel($targetPath);
                        }
                    }

                    
                    break;
                default:
                    return [$CollCom];
            }
//            return $CollCom;
        }
        $this->View->admincontent = $this->View->execute('users.html');
        if($this->POST){
            return ['Content'=>$this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function GetpagelistAction() {
        $page = new PageModel();
        return $page->GetList(json_decode($this->REQUEST));
    }
    public function AddpageAction() {
        $PostData = json_decode($this->REQUEST, true);
        $page = new PageModel();
        return ['success' => true, 'id' => $page->Add($PostData)];
    }
    public function DelpageAction() {
        $PostData = json_decode($this->REQUEST, true);
        $page = new PageModel();
        return ['success' => $page->Del($PostData['id'])];
    }
    public function EditpageAction($id) {
        $PostData = json_decode($this->REQUEST, true);
        $page = new PageModel();
        return ['success' => true, 'id' => $page->Edit($PostData,$id)];
    }

    public function PagesAction() {
        $this->View->admincontent = $this->View->execute('pages.html');
        if ($this->POST) {
            return ['Content' => $this->View->admincontent];
        }
        $this->View->content = $this->View->execute('AdminWraper.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function GetMenuAction() {
        $Data = [
//            ['id' => '1', 'parent' => '0', 'name' => 'Главная', 'src' => '/admin', 'class' => 'fas fa-home'],
            ['id' => '2', 'parent' => '0', 'name' => 'Пользователи', 'src' => '/admin/user', 'class' => 'fas fa-users-cog'],
            ['id' => '3', 'parent' => '0', 'name' => 'Страницы', 'src' => '/admin/pages', 'class' => 'far fa-file'],
//            ['id' => '4', 'parent' => '3', 'name' => 'wisiwing', 'src' => '/admin/editor/tinymce', 'class' => 'far fa-file'],
//            ['id' => '5', 'parent' => '0', 'name' => 'Конфигуратор','src'=>'/admin/Configurator','class'=>'fas fa-calculator']
        ];
        return $Data;
    }

}
