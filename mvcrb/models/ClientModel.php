<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of UserModel
 *
 * @author ivank
 */
class ClientModel extends Model {

    private $TableName;

    public function __construct() {
        parent::__construct();   
        Session::init();
        Session::set('BrowserHesh', mvcrb::BrouserHash());
        $this->TableName = 'client';
    }
    public function GetCountUser() {
        return $this->count($this->TableName);
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
            $tempbean = $this->findAll($this->TableName, 'ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->findAll($this->TableName, 'LIMIT ' . $start . ', ' . $limit);
        }
        if ($tempbean) {

            $tempdata = $this->exportAll($tempbean, TRUE);
            foreach ($tempdata as $key => $value) {
                // удаляю пароли из массива
                // нехуя их ваще посылать кудато. да они захешированы но всеравно ниннааадаа
                unset($tempdata[$key]['password']);
                unset($tempdata[$key]['ownCheck']);
            }
            $List['data'] = $tempdata;
            return $List;
        }
        return FALSE;
    }

    public function GetPartnerList($PostData = null) {
        $start = $PostData->start ? $PostData->start : 0;
        $limit = $PostData->limit ? $PostData->limit : 10;
        $List['count'] = $this->count($this->TableName,'WHERE role = 300');
        if (isset($PostData->data) && $PostData->data !== '') {
            $order['data'] = $PostData->data;
            $order['dir'] = $PostData->dir;
        } else {
            $order = null;
        }

        if (is_array($order)) {
            $tempbean = $this->findAll($this->TableName, 'WHERE role = 300 ORDER BY ' . $order['data'] . ' ' . $order['dir'] . ' LIMIT ' . $start . ', ' . $limit);
        } else {
            $tempbean = $this->findAll($this->TableName, 'WHERE role = 300 LIMIT ' . $start . ', ' . $limit);
        }

        if ($tempbean) {

            $tempdata = $this->exportAll($tempbean, TRUE);
            foreach ($tempdata as $key => $value) {
                // удаляю пароли из массива
                // нехуя их ваще посылать кудато. да они захешированы но всеравно нннааадаа
                unset($tempdata[$key]['password']);
                unset($tempdata[$key]['ownCheck']);
            }
            $List['data'] = $tempdata;
            return $List;
        }
        return FALSE;
    }

    public function GetUserID($id) {
        $u = $this->findOne($this->TableName, 'id = ?', array($id));
        if ($u) {
            return $u->export();
        }
        return FALSE;
    }
    public function GetUserFromEmail($email) {
        $u = $this->findOne($this->TableName, 'email = ?', array($email));
        if ($u) {
            return $u->export();
        }
        return null;
    }
    public function GetUserFromCode($Code) {
        $u = $this->findOne($this->TableName, 'resetpass = ?', array($Code));
        if ($u) {
            return $u->export();
        }
        return null;
    }

    public function DellUser($id = 0) {
        $User = $this->load($this->TableName, $id);
        return $this->trash($User);
    }

    public function GetCurrentUser() {
        $var = Session::get('LoggedUser');
        $AnonimUser = ['Name' => 'anonim', 'login' => 'anonim', 'role' => 0];
//        dd($var);
        if ($var) {
            $user = $this->findOne($this->TableName, 'email = ?', array($var['email']));

            if ($user) {
                $ret = $user->export();
                $ret['fakerole']=$var['fakerole'];
                unset($ret["password"]);
                unset($ret["token"]);
                unset($ret["resetpass"]);
                unset($ret["ip"]);
                unset($ret["browser"]);
                return $ret;
            }
            return $AnonimUser;
        }

        return $AnonimUser;
    }

    public function ChekMail($mail) {

        if ($this->count($this->TableName, "email = ?", array($mail)) > 0) {
//            $errors[] = 'Пользователь с таким Email уже существует!';
            return TRUE;
        }
        return FALSE;
    }

    public function ChekUserLogin($login) {
        //проверка на существование одинакового логина
        if ($this->count($this->TableName, "login = ?", [$login]) > 0) {
//            $errors[] = 'Пользователь с таким логином уже существует!';
            return TRUE;
        }
        return FALSE;
    }
    public function ChekUserPhone($phone) {
        //проверка на существование одинакового телефона
        if ($this->count($this->TableName, "phone = ?", [$phone]) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function CreateUser($email, $password, $login, $role = 100, $firstname = '', $lastname = '', $phone = '', $registredatetime = '') {

        $user = $this->Dispense($this->TableName);
        $user->firstname = $firstname;
//        $user->middlename = $middlename;
        $user->lastname = $lastname;

        $user->login = $login;
        $user->email = $email;
        $user->phone = $phone;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        //пароль нельзя хранить в открытом виде, 
        //мы его шифруем при помощи функции password_hash для php > 5.6

        $user->role = empty($role) ? 100 : $role;

//        $user->activation = TRUE;
        if ($registredatetime == '') {
            $registredatetime = date('Y-m-d H:i:s');
        }
        $user->registredatetime = $registredatetime;

        return $this->store($user);
    }
    public function CreateFullUser($Data=null) {
        if(!$Data)return false;
        $user = $this->Dispense($this->TableName);
        $password = $Data['password'];
        unset($Data['password']);
        unset($Data['fpassword']);
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        //пароль нельзя хранить в открытом виде, 
        //мы его шифруем при помощи функции password_hash для php > 5.6
        $user->import($Data);
        //$user->role = 100;

        $user->registredatetime = date('Y-m-d H:i:s');

        return $this->store($user);
    }
    public function EditUser($email, $password, $login, $role = 100, $firstname = '', $lastname = '', $phone = '', $id = 0) {

//        $user = $this->dispense($this->TableName);
        $user = $this->findOne($this->TableName, 'id = ?', array($id));
        $user->firstname = $firstname;
//        $user->middlename = $middlename;
        $user->lastname = $lastname;

        $user->login = $login;
        $user->email = $email;
        $user->phone = $phone;
        //пароль нельзя хранить в открытом виде, 
        //мы его шифруем при помощи функции password_hash для php > 5.6
        if ($password <> '') {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }

        $user->role = $role;

        return $this->store($user);
    }
    public function ChangePassword($userID,$password) {
        $user = $this->findOne($this->TableName, 'id = ?', array($userID));
        if ($password <> '') {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }
        return $this->store($user);
    }
    public function PasswordCodeEmailReset($userID, $Code=null) {
        $user = $this->findOne($this->TableName, 'id = ?', array($userID));
        if ($Code ) {
            $user->resetpass = $Code;
        }
        return $this->store($user);
    }
    public function PasswordCodeEmailDele($userID) {
        $user = $this->findOne($this->TableName, 'id = ?', array($userID));
        
        $user->resetpass = null;
      
        return $this->store($user);
    }
    public function ChangeDataUser($userID, $Data) {
//        dd($Data);
        $user = $this->findOne($this->TableName, 'id = ?', array($userID));
        unset($Data['password']); // убираем хеш пароля.ownKkm
        unset($Data['ownKkm']);
        $user->import($Data);
        return $this->store($user);
    }

    public function login($email, $password) {
        $user = $this->findOne($this->TableName, '(email = :email) or (phone= :email)', [':email' => $email]);

        if ($user) {
            //логин существует
            if (password_verify($password, $user->password)) {
                //если пароль совпадает, то нужно авторизовать пользователя

                $user->lastlogin = date('Y-m-d H:i:s');
                $user->browser = $_SERVER['HTTP_USER_AGENT'];
                $user->ip = $_SERVER['REMOTE_ADDR'];
//                $user->session = mvcrb::BrouserHash();
                $this->store($user);
                $VarUser = $user->export();
                unset($VarUser['password']); // убираем хеш пароля.
                Session::set('LoggedUser', $VarUser);
                return TRUE;
            }
        }

        return FALSE;
    }
    public function GetUserFromToken($UserToken) {
        $user = $this->findOne($this->TableName, '(token = :token)', [':token' => $UserToken]);
        if ($user) {
            return $user->export();
        } else {
            return null;
        }
    }
    public function GenerateAccessToken($UserId) {
        $AccessToken = $this->UniqIdReal();
        $User = $this->load($this->TableName, $UserId);
        $User->token = $AccessToken;
        $this->store($User);
        return $AccessToken;
    }
    /**
     * Генерируеут рандомный UID
     * https://www.php.net/manual/ru/function.uniqid.php#120123
    */
    public function UniqIdReal($lenght = 64){
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if(function_exists("openssl_random_pseudo_bytes")){
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        }elseif(function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        }else{
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
    public function GeneratePassword($length = 20) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
                '0123456789`-=!@#$%^&*()_+,.?';

        $str = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++)
            $str .= $chars[random_int(0, $max)];

        return $str;
    }

}
