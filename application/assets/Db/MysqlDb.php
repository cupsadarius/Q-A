<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/5/14
 * Time: 11:20 AM
 */

namespace application\assets\Db;


class MysqlDb {

    private $db;
    private $host;
    private $user;
    private $password;
    private $type;

    public function __construct(){
        $this->db = "internship";
        $this->host = "localhost";
        $this->user = "internship";
        $this->password = "axeqwdarius";
        $this->type = "mysql";
    }

    /**
     * @return string
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

} 