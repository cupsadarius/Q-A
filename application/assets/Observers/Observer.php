<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/4/14
 * Time: 6:40 PM
 */

namespace application\assets\Observers;


use application\assets\Entities\Question;

interface Observer {

    public function __construct();
    public function update(Question $obj);
} 