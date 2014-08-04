<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/1/14
 * Time: 10:03 AM
 */

namespace application\assets\Routes;
use application\assets\Request;
class Route {

    private $request;
    private $type;
    private $controller;
    private $callback;
    private $pattern;

    public function __construct($type,$pattern,$controller,$callback){
        $this->request = new Request();
        $this->type = $type;
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->callback = $callback;
        $this->populateRequest();
        return $this;
    }

    private function populateRequest(){
        if($this->type == 'post'){
            foreach($_POST as $key=>$value){
                $this->request->push($key,htmlentities($value));
            }
        } else {
            if(isset($this->pattern)&&($this->pattern != '/')){
                preg_match_all('/(?<={).*?(?=})/',$this->pattern,$keys);
                $patern_root = substr($this->pattern,0,strpos($this->pattern,'{',1));
                $keys = $keys[0];
                $vals = explode('/',substr($_SERVER['REQUEST_URI'],strlen($patern_root),strlen($_SERVER['REQUEST_URI'])-strlen($patern_root)));
                foreach($keys as $key=>$value){
                    $val = (isset($vals[$key])&&!empty($vals[$key]))?htmlentities($vals[$key]):0;
                    $this->request->push($value,$val);
                }
            }
        }
        return $this;
    }

    public function getUri(){
        $patern_root = substr($this->pattern,0,strpos($this->pattern,'{',1));
        if(($this->pattern == '/') ||(empty($patern_root))){
            return $this->pattern;
        }
        foreach($this->request as $value){
            $patern_root .=$value.'/';
        }
        return substr($patern_root,0,strlen($patern_root)-1);
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return \application\assets\Request
     */
    public function getRequest()
    {
        return $this->request;
    }


}

