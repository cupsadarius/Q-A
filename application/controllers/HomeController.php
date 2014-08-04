<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 9:41 AM
 */

namespace application\controllers;
use application\assets\Registry;
use application\assets\Request;

class HomeController {

    public function indexAction(Registry $app, Request $request){
        $data['sections'] = $app->sections;
        $data['base_url'] = $app->base_url;
        $data['user'] = $app->auth->loggedUser();
        $data['page'] = 'home';
        $data['section'] = '';
        $guestModel = $app->modelFactory->buildGuestModel();
        $data['questions'] = $guestModel->getQuestions(null,10,0);
        echo $app->twig->render('home.html.twig',array('data'=>$data));
    }

    public function viewSectionAction(Registry $app, Request $request){
        $data['sections'] = $app->sections;
        $data['base_url'] = $app->base_url;
        $data['section'] = $request->tag;
        $data['user'] = $app->auth->loggedUser();
        $guestModel = $app->modelFactory->buildGuestModel();
        $offset = isset($request->offset)?$request->offset:0;
        $questions = $guestModel->getQuestionsInSection($request->tag,10,$offset);
        $data['current_section'] = $questions[0];
        $data['questions'] = $questions[1];
        $data['offset'] = $offset;
        $data['total'] = count($data['questions']);
        $data['page'] = '';
        echo $app->twig->render('section.html.twig',array('data'=>$data));
    }

    public function viewQuestionAction(Registry $app, Request $request){
        $data['sections'] = $app->sections;
        $data['base_url'] = $app->base_url;
        $data['alert'] = isset($_SESSION['alert'])?$_SESSION['alert']:null;
        unset($_SESSION['alert']);
        $data['page'] = '';
        $data['user'] = $app->auth->loggedUser();
        $guestModel = $app->modelFactory->buildGuestModel();
        $question = $guestModel->getQuestions(array('id' => $request->id));
        $data['question'] = $question[0];
        $data['answers'] = $guestModel->getAnswers(array('question_id'=>$question[0]->getId()));
        $data['section'] = $data['question']->getSection()->getTag();
        echo $app->twig->render('question.html.twig',array('data'=>$data));
    }
}