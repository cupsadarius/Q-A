<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/31/14
 * Time: 1:39 PM
 */

namespace application\models;


use application\assets\Registry;

class ModelFactory {
    private $app;

    public function __construct(Registry $app){
        $this->app = $app;
    }
    public function buildGuestModel(){
        return new GuestModel($this->app);
    }
    public function buildMemberModel(){
        return new MemberModel($this->app);
    }
} 