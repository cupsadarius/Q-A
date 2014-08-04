<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/31/14
 * Time: 1:39 PM
 */

namespace application\models;


use application\assets\Registry;

class GuestModel {

    private $app;

    public function __construct(Registry $app){
        $this->app = $app;
    }

    public function getSections(){
        $sectionMapper = $this->app->em->buildSectionMapper();
        return $sectionMapper->find();
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
    public function getQuestionsInSection($section,$limit = null, $offset = null){
        $sectionMapper = $this->app->em->buildSectionMapper();
        $currentSection = $sectionMapper->find(array('tag'=>$section));
        $questions = $this->getQuestions(array('section_id'=>$currentSection->getId()),$limit,$offset);
        return array($currentSection->getName(),$questions);
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
} 