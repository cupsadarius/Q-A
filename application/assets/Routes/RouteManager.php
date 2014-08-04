<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/1/14
 * Time: 12:06 PM
 */

namespace application\assets\Routes;


class RouteManager {

    private  $routes = array();
    public function __construct(){

    }

    public function addRoute(Route $route,$name = null)
    {
        array_push($this->routes, array('name'=>$name,'route'=>$route));
    }

    public function getMatchedRoute(){
        foreach($this->routes as $route){
            if($_SERVER['REQUEST_URI'] == $route['route']->getUri()){
                return $route;
            }
        }
    }
    public function getRoute($route_name){
        foreach($this->routes as $route){
            if($route['name'] == $route_name){
                return $route;
            }
        }
        return false;
    }

} 