<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/4/14
 * Time: 4:39 PM
 */

namespace application\assets;


class MailService {

    private $_to;
    private $_from;
    private $_subject;
    private $_message;
    private $_headers;

    public function __construct(){

    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->_from = $from;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }

    public function mail(){
        $headers = "From: $this->_from\r\n";
        $headers .= "Content-type: text/html\r\n";
        $this->setHeaders($headers);
        mail($this->_to,$this->_subject,$this->_message,$this->_headers);
    }

} 