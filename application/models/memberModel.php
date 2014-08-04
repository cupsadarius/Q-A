<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/31/14
 * Time: 1:39 PM
 */

namespace application\models;


use application\assets\Registry;

class MemberModel {

    private $app;
    public function __construct(Registry $app){
        $this->app = $app;
    }
    public function getSections(array $conditions = null){
        $sectionMapper = $this->app->em->buildSectionMapper();
        return $sectionMapper->find($conditions);
    }
    public function getQuestions(array $conditions = null,$limit = null,$offset = null){
        $questionMapper = $this->app->em->buildQuestionMapper();
        $conditions = is_null($conditions)?null:$conditions;
        $questions = $questionMapper->find($conditions,'id','DESC',$offset,$limit);
        if(!is_null($questions)){
            if(!is_array($questions)){
                $questions = array();
                $questions[0] = $questionMapper->find($conditions,'id','DESC',$offset,$limit);

            }
        }
        return $questions;
    }

    public function getAnswers(array $conditions = null,$limit = 10,$offset = 0){
        $answerMapper = $this->app->em->buildAnswerMapper();
        $conditions = is_null($conditions)?null:$conditions;

        $answers = $answerMapper->find($conditions,'id','DESC',$offset,$limit);
        if(!is_null($answers)){
            if(!is_array($answers)){
                $answers = array();
                $answers[0] = $answerMapper->find($conditions,'id','DESC',$offset,$limit);
            }
        }
        return $answers;
    }

    public function getSubscriptions(array $conditions = null,$limit = null,$offset = null){
        $subscriptionMapper = $this->app->em->buildSubscriptionMapper();
        $conditions = is_null($conditions)?null:$conditions;

        $subscription = $subscriptionMapper->find($conditions,'id','DESC',$offset,$limit);
        if(!is_null($subscription)){
            if(!is_array($subscription)){
                $subscription = array();
                $subscription[0] = $subscriptionMapper->find($conditions,'id','DESC',$offset,$limit);
            }
        }
        return $subscription;
    }
} 