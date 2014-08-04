<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 10:16 AM
 */

namespace application\assets;


class Request {

    public function push($key,$value){
        $this->$key=$value;
    }
}