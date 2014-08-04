<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/31/14
 * Time: 11:19 AM
 */

namespace application\assets;


use application\assets\Entities\User;

class Auth {

    private $app;
    private $loggedUser = null;


    public function __construct(Registry $app){
        $this->app = $app;
    }

    public function DoubleSaltedHash($pw, $salt) {
        return sha1($salt.sha1($salt.sha1($pw)));
    }

    public function generate_salt() {
        $dummy = array_merge(range('0', '9'));
        mt_srand((double)microtime()*1000000);
        for ($i = 1; $i <= (count($dummy)*2); $i++)
        {
            $swap = mt_rand(0,count($dummy)-1);
            $tmp = $dummy[$swap];
            $dummy[$swap] = $dummy[0];
            $dummy[0] = $tmp;
        }
        return sha1(substr(implode('',$dummy),0,9));
    }

    public function login(Request $request,User $user = null){
        $userMapper = $this->app->em->buildUserMapper();
        $user = (is_null($user))?$userMapper->find(array('username'=>$request->username)):$user;
        if(is_null($user)) return false;
        if($user->getPassword() == $this->DoubleSaltedHash($request->password,$user->getSalt())){
            $this->loggedUser = $user;
            $_SESSION['uid'] = $user->getId();
            return true;
        }
        return false;
    }
    public function loginHash(Request $request){
        $userMapper = $this->app->em->buildUserMapper();
        if($user = $userMapper->find(array('hash'=>$request->hash))){
            $this->loggedUser = $user;
            $_SESSION['uid'] = $user->getId();
            return true;
        }
        return false;
    }

    public function setLoggedUser(User $user){
        $this->loggedUser = $user;
    }

    public function loggedUser(){
        if(!is_null($this->loggedUser)){
            return $this->loggedUser;
        }else{
            if(isset($_SESSION['uid'])){
                $userMapper = $this->app->em->buildUserMapper();
                $this->loggedUser = $userMapper->find(array('id'=>htmlentities($_SESSION['uid'])));
                return $this->loggedUser;
            }
        }
        return false;
    }
    public function logout(){
        $this->loggedUser = null;
        session_destroy();
        return true;
    }
}

