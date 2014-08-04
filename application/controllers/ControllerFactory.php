<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 9:42 AM
 */

namespace application\controllers;


class ControllerFactory {

    public function buildHomeController(){
        return new HomeController();
    }
    public function buildMemberController(){
        return new MemberController();
    }
} 