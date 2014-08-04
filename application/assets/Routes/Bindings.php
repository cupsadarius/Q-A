<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/1/14
 * Time: 11:35 AM
 */

namespace application\assets\Routes;


class Bindings {

    private $bindings = array();

    public function before($route_name,$condition){
        array_push($this->bindings,array('type'=>'before','route_name'=>$route_name,'condition'=>$condition));
    }

    public function after($route_name,$condition){
        array_push($this->bindings,array('type'=>'after','route_name'=>$route_name,'condition'=>$condition));
    }

    public function getBind($route_name){
        foreach($this->bindings as $bind){
            if($bind['route_name'] == $route_name){
                return $bind;
            }
        }
        return false;

    }


} 