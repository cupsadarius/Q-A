<?php


namespace application\assets;
use application\assets\Entities\User;
use application\assets\DataMappers\UserMapper;
use application\assets\Entities\Question;
use application\assets\DataMappers\QuestionMapper;
use application\assets\Entities\Section;
use application\assets\DataMappers\SectionMapper;
use application\assets\Entities\Answer;
use application\assets\DataMappers\AnswerMapper;
use application\assets\Entities\Subscription;
use application\assets\DataMappers\SubscriptionMapper;
use application\assets\Db\DbConnection;
class EntityManager {

    private $_db;
    public function __construct(DbConnection $db){
        $this->_db = $db;
    }

    public function buildUser(){
        return new User();
    }
    public function buildUserMapper(){
        return new UserMapper($this->_db);
    }
    public function buildSection(){
        return new Section();
    }
    public function buildSectionMapper(){
        return new SectionMapper($this->_db);
    }
    public function buildQuestion(){
        return new Question();
    }
    public function buildQuestionMapper(){
        return new QuestionMapper($this->_db);
    }
    public function buildAnswer(){
        return new Answer();
    }
    public function buildAnswerMapper(){
        return new AnswerMapper($this->_db);
    }
    public function buildSubscription(){
        return new Subscription();
    }
    public function buildSubscriptionMapper(){
        return new SubscriptionMapper($this->_db);
    }


} 