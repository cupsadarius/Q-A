<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 12:21 PM
 */

namespace application\controllers;

use application\assets\Entities\User;
use application\assets\Registry;
use application\assets\Request;

class MemberController
{


    public function loginAction(Registry $app, Request $request, User $user = null)
    {
        if(session_status() == PHP_SESSION_ACTIVE){
            session_destroy();
            session_start();
        }else{
            session_start();
        }
        if (isset($request->hash)) {
            $is_logged = $app->auth->loginHash($request);
        } else {
            $is_logged = $app->auth->login($request, $user);
        }
        if ($is_logged) {
            $app->redirect(200, $app->base_url . '/profile');
        } else {
            $data['page'] = '';
            $data['base_url'] = $app->base_url;
            echo $app->twig->render('login.html.twig', array('data' => $data));
        }
    }

    public function logoutAction(Registry $app, Request $request = null)
    {
        if ($app->auth->logout()) {
            $data['page'] = '';
            $data['base_url'] = $app->base_url;
            echo $app->twig->render('login.html.twig', array('data' => $data));
        }
    }

    public function dashboardAction(Registry $app, Request $request = null)
    {
        $memberModel = $app->modelFactory->buildMemberModel();
        $data['sections'] = $memberModel->getSections();
        $data['user'] = $app->auth->loggedUser();
        $data['action'] = $request->action;
        $data['page'] = 'dashboard';
        $data['base_url'] = $app->base_url;
        if($data['action'] == 'overview'){
            $data['questions'] = $memberModel->getQuestions(array('user_id'=>$data['user']->getId()));
            $data['answers'] = $memberModel->getAnswers(array('user_id'=>$data['user']->getId()));
            echo $app->twig->render('dashboard.html.twig', array('data' => $data));
        }
        if($data['action'] == 'subscription'){
            $data['subscriptions'] = $memberModel->getSubscriptions(array('user_id'=>$data['user']->getId()));
            echo $app->twig->render('subscriptions.html.twig', array('data' => $data));
        }



    }

    public function profileAction(Registry $app, Request $request)
    {
        $data['user'] = $app->auth->loggedUser();
        $data['alert'] = isset($_SESSION['alert'])?$_SESSION['alert']:null;
        unset($_SESSION['alert']);
        $data['page'] = 'profile';
        $data['base_url'] = $app->base_url;
        echo $app->twig->render('profile.html.twig', array('data' => $data));

    }

    public function editUserAction(Registry $app, Request $request){
        $user = $app->auth->loggedUser();
        if($user->getPassword() == $app->auth->DoubleSaltedHash($request->current_password,$user->getSalt())){
            $user->setFirstName(htmlentities($request->firstName));
            $user->setLastName(htmlentities($request->lastName));
            $user->setEmail(htmlentities($request->email));
            $user->setUsername(htmlentities($request->username));
            if(!empty($request->password)){
                $user->setPassword($app->auth->DoubleSaltedHash($request->password, $user->getSalt()));
            }
            $user->setHash($app->auth->DoubleSaltedHash($user->getFirstName(), $user->getLastName()));
            $user->setBirthday(new \DateTime($request->birthday));
            $userMapper = $app->em->buildUserMapper();
            $userMapper->save($user);
            $app->auth->setLoggedUser($user);
            $app->redirect(200,$app->base_url."/profile");
        } else {
            $req = new Request();
            $_SESSION['alert'] = array('type'=>'danger','message'=>'The current password doesn\'t match');
            $this->profileAction($app,$req);
        }
    }

    public function deleteUserAction(Registry $app, Request $request){
        $userMapper = $app->em->buildUserMapper();
        $userMapper->delete($app->auth->loggedUser());
        session_destroy();
        $app->redirect(200,$app->base_url);
    }

    public function registerAction(Registry $app, Request $request)
    {
        $user = $app->em->buildUser();
        $salt = $app->auth->generate_salt();
        $user->setFirstName(htmlentities($request->firstName));
        $user->setLastName(htmlentities($request->lastName));
        $user->setEmail(htmlentities($request->email));
        $user->setUsername(htmlentities($request->username));
        $user->setSalt($salt);
        $user->setPassword($app->auth->DoubleSaltedHash($request->password, $salt));
        $user->setHash($app->auth->DoubleSaltedHash($user->getFirstName(), $user->getLastName()));
        $user->setBirthday(new \DateTime($request->birthday));
        $userMapper = $app->em->buildUserMapper();
        $userMapper->save($user);
        //log user
        $app->auth->login($request,$userMapper->find(array('username' => htmlentities($request->username))));
        $data['user'] = $user;
        $data['base_url'] = $app->base_url;
        $data['page'] = '';

        echo $app->twig->render('registerSuccessfully.html.twig', array('data' => $data));

    }

    public function postAnswerAction(Registry $app, Request $request)
    {
        $answerMapper = $app->em->buildAnswerMapper();
        $questionMapper = $app->em->buildQuestionMapper();
        $answer = $app->em->buildAnswer();
        $answer->setTitle($request->title);
        $answer->setAnswer($request->answer);
        $answer->setUser($app->auth->loggedUser());
        $answer->setQuestion($questionMapper->find(array('id' => $request->question_id)));
        $answer->setRating('0|0');
        $answer->setModifiedDate(new \DateTime());
        $answerMapper->save($answer);
        $app->redirect(200, "$app->base_url/question/view/$request->question_id");

    }

    public function rateAnswerAction(Registry $app, Request $request)
    {
        $answerMapper = $app->em->buildAnswerMapper();
        $answer = $answerMapper->find(array('id' => $request->answer_id));
        $rating = explode('|', $answer->getRating());
        $answerMapper = $app->em->buildAnswerMapper();
        $answer = $answerMapper->find(array('id' => $request->answer_id));
        $rating = explode('|', $answer->getRating());
        if ($request->rating == 1) {
            $rating[0] += 1;
        } elseif ($request->rating == -1) {
            $rating[1] += 1;
        }

        $answer->setRating(implode('|', $rating));
        $answerMapper->save($answer);

        echo $rating[0] . " " . $rating[1];
    }

    public function editAnswerAction(Registry $app, Request $request){
        $answerMapper = $app->em->buildAnswerMapper();
        $answer = $answerMapper->find(array('id'=>$request->answer_id));
        $answer->setTitle($request->title);
        $answer->setAnswer($request->answer);
        $answer->setModifiedDate(new \DateTime());
        $answerMapper->save($answer);
        $app->redirect(200,$app->base_url."/dashboard/overview");
    }

    public function getJsonAnswerAction(Registry $app, Request $request){
        $answerMapper = $app->em->buildAnswerMapper();
        $answer = $answerMapper->find(array('id'=>$request->answer_id));
        $response = array(
            'id' => $answer->getId(),
            'user_id' => $answer->getUser()->getId(),
            'question_id'=> $answer->getQuestion()->getId(),
            'title'=>$answer->getTitle(),
            'answer'=>$answer->getAnswer()
        );
        echo json_encode($response);
    }

    public function deleteAnswerAction(Registry $app, Request $request){
        $answerMapper = $app->em->buildAnswerMapper();
        $answer = $answerMapper->find(array('id'=>$request->answer_id));
        $answerMapper->delete($answer);
        $app->redirect(200,$app->base_url."/dashboard/overview");
    }

    public function addQuestionAction(Registry $app, Request $request){
        $questionMapper = $app->em->buildQuestionMapper();
        $sectionMapper = $app->em->buildSectionMapper();
        $question = $app->em->buildQuestion();
        $question->setUser($app->auth->loggedUser());
        if(($request->section == '0') && (!empty($request->section_new))){
            $section = $app->em->buildSection();
            $section->setTag(strtolower($request->section_new));
            $section->setName(ucfirst($request->section_new));
            $sectionMapper->save($section);
            $question->setSection($sectionMapper->find(array('tag'=>$section->getTag())));
        }else {
            $section = $sectionMapper->find(array('id'=>$request->section));
            $question->setSection($section[0]);
        }
        $question->setQuestion($request->question);
        $question->setDescription($request->description);
        $question->setModifiedDate(new \DateTime());
        $questionMapper->save($question);
        $app->redirect(200,$app->base_url."/dashboard/overview");

    }

    public function editQuestionAction(Registry $app, Request $request){
        $questionMapper = $app->em->buildQuestionMapper();
        $memberModel = $app->modelFactory->buildMemberModel();
        $question = $questionMapper->find(array('id'=>$request->question_id));
        $question->setSection($memberModel->getSections(array('id'=>$request->section)));
        $question->setQuestion($request->question);
        $question->setDescription($request->description);
        $question->setModifiedDate(new \DateTime());
        $questionMapper->save($question);
        $app->redirect(200,$app->base_url."/dashboard/overview");
    }

    public function getJsonQuestionAction(Registry $app, Request $request){
        $questionMapper = $app->em->buildQuestionMapper();
        $question = $questionMapper->find(array('id'=>$request->question_id));
        $response = array(
            'id' => $question->getId(),
            'user_id' => $question->getUser()->getId(),
            'section_id'=> $question->getSection()->getId(),
            'question'=>$question->getQuestion(),
            'description'=>$question->getDescription()
        );
        echo json_encode($response);
    }

    public function deleteQuestionAction(Registry $app, Request $request){
        $questionMapper = $app->em->buildQuestionMapper();
        $question = $questionMapper->find(array('id'=>$request->question_id));
        $questionMapper->delete($question);
        $app->redirect(200,$app->base_url."/dashboard/overview");
    }

    public function addSubscriptionAction(Registry $app, Request $request){
        $subscriptionMapper = $app->em->buildSubscriptionMapper();
        $user = $app->auth->loggedUser();
        if($subscriptionMapper->find(array('user_id'=>$user->getId(),'question_id'=>$request->question_id))){
            $_SESSION['alert'] = array('type'=>'warning','message'=>'You are already subscribed!');
            $app->redirect(200,$app->base_url."/question/view/".$request->question_id);
        }else {
            $memberModel = $app->modelFactory->buildMemberModel();
            $question = $memberModel->getQuestions(array('id'=>$request->question_id),1);
            $subscription = $app->em->buildSubscription();
            $subscription->setUser($app->auth->loggedUser());
            $subscription->setQuestion($question[0]);
            $subscriptionMapper->save($subscription);
            $app->redirect(200,$app->base_url."/question/view/".$request->question_id);
        }

    }

    public function deleteSubscriptionAction(Registry $app, Request $request){
        $subscriptionMapper = $app->em->buildSubscriptionMapper();
        $subscription = $subscriptionMapper->find(array('id'=>$request->subscription_id));
        $subscriptionMapper->delete($subscription);
        $app->redirect(200,$app->base_url."/dashboard/subscription");
    }


}