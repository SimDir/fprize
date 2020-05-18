<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of PageController
 *
 * @author ivan p kolotilkin
 */
class PageController extends Controller {

    public function IndexAction($category = '', $page = '') {
        $this->View->title = 'Все страници';
        $this->View->PageArr = $this->GetModel('Page')->GetAllPage($category);
//        dd($vdl);
        $this->View->content = $this->View->execute('PageAll.html');
        $this->View->content = $this->View->execute('page.html');
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function TestAction($par = 0) {
        $vdl = $this->GetModel('Page')->Get($par);
//        $tmp = $vdl->Get($par);
        dd($vdl);
    }

    public function PageAction($page) {

        $Pages = $this->GetModel('Page');
        $PRet = $Pages->GetPage($page);

        $User = new UserModel();
        $curUser = $User->GetCurrentUser();
//        dd($curUser);
        if ($curUser['role'] >= 300) {
            $this->view->VarSetArray($curUser);
            $this->View->pageid = $PRet['id'];
            $this->View->adminpanel = $this->View->execute('AdminBar.html', TEMPLATE_DIR . 'AdminController' . DS);
        }
        if ($PRet) {

//            dd($PRet);
            $this->View->content = $PRet['content'];
            $this->View->title = $PRet['title'];
            $this->View->content = $this->View->execute('page.html');
//            return $this->View->execute('index.html', TEMPLATE_DIR);
        }





//        $this->View->content = $this->View->execute('pages.html',TEMPLATE_DIR.'IndexController'.DS);
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function EditAction($id = 0) {
        $User = new UserModel();
        if ($User->GetCurrentUser()['role'] < 300) {
            return mvcrb::Redirect('/error/404');
        }
        $Page = new PageModel();
        $p = $Page->GetPage($id);
        $this->View->apikey = '2vf7bxyhwhgjglrd05fapr353yk2xhegpnqdxgnhdk78rsie';
        $this->View->EditorText = $p['content'];
        $this->View->id = $p['id'];
        $this->View->name = $p['name'];
        $this->View->content = $this->View->execute('Editor.html', TEMPLATE_DIR . 'AdminController' . DS);
        return $this->View->execute('index.html', TEMPLATE_DIR);
    }

    public function SaveAction($param = null) {
        $User = new UserModel();
        $curUser = $User->GetCurrentUser();
        if ($curUser['role'] < 300) {
            return ['acces_denide'];
        }
        $postdata = json_decode($this->REQUEST, true);
        $Data['author'] = $curUser['login'];
        $Data['type'] = 'page';
        $Data['content'] = htmlspecialchars_decode($postdata['PageContent'], ENT_HTML5);
        $Pages = new PageModel();

        return $Pages->Edit($Data, (int) $postdata['PageId']);
    }

}
