<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/4/14
 * Time: 4:39 PM
 */

namespace application\assets\Observers;


use application\assets\Entities\Question;
use application\assets\MailService;
use application\assets\Registry;
class QuestionObserver implements Observer{

    private $_mail;
    private $_app;

    public function __construct(){
        $this->_mail = new MailService();
        $this->_app = Registry::getInstance();
    }

    public function update(Question $obj){
        $subscriptionMapper = $this->_app->em->buildSubscriptionMapper();
        $subcribers = $subscriptionMapper->find(array('question_id'=>$obj->getId()));
        if(!is_null($subcribers)){
            if(!is_array($subcribers)){
                $tmp = $subcribers;
                $subcribers = array();
                $subcribers[0] = $tmp;
            }
            foreach($subcribers as $subscriber){
                $user = $subscriber->getUser();
                $this->_mail->setFrom('webmaster@QandA.com');
                $this->_mail->setTo($user->getEmail());
                $this->_mail->setSubject("Changes at: ".$obj->getQuestion());
                $this->_mail->setMessage("<html><body><table border='0'><tr><td>Title:</td><td>".$obj->getQuestion()."</td></tr><tr><td>Description:</td><td>".$obj->getDescription()."</td></tr></table> </body></html>");
                $this->_mail->mail();
            }
        }
    }

} 