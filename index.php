<?php

use application\assets\Registry;
use application\assets\Db\DbAdapter as DB;
use application\assets\Db\MysqlDb;
use application\assets\Routes\RouteManager;
use application\assets\Routes\Route;
use application\controllers\ControllerFactory;
use application\assets\EntityManager;
use application\assets\Auth;
use application\models\ModelFactory;
use application\assets\Routes\Constrains;
use application\assets\Routes\Bindings;

spl_autoload_extensions(".php");
spl_autoload_register();

require_once __DIR__.'/application/lib/Twig/Autoloader.php';

$app = Registry::getInstance();

session_start();

$app->register('dev_mode',true);
$app->register('base_url','http://qanda.local');
if($app->dev_mode){
    function exception_handler(Exception $e){
        file_put_contents(__DIR__.'/logs/Q&A.log',"Exception caught: ".$e->getMessage()."\n",FILE_APPEND);
    }
    set_exception_handler('exception_handler');
}

//Twig register
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(__DIR__.'/application/views');
$twig = new Twig_Environment($loader);

//register services
$app->register('twig',$twig);
$app->register('controllerFactory',new ControllerFactory());
$app->register('modelFactory', new ModelFactory($app));
$app->register('em',new EntityManager(DB::getInstance(new MysqlDb())));
$app->register('auth', new Auth($app));
//controllers
$home = $app->controllerFactory->buildHomeController();
$member = $app->controllerFactory->buildMemberController();

//models
$guestModel = $app->modelFactory->buildGuestModel();
$app->register('sections',$guestModel->getSections());

//define conditions
$conditions = new Constrains();
$conditions->register('is_logged',function($app){
   if($app->auth->loggedUser()){
        return true;
   }
    return false;
});
$app->register('conditions',$conditions);

//define routes
$routeManager = new RouteManager();
//guest routes
$routeManager->addRoute(new Route('get','/',$home,'indexAction'),'home');
$routeManager->addRoute(new Route('get','/section/{tag}/{offset}',$home,'viewSectionAction'),'sections');
$routeManager->addRoute(new Route('get','/question/view/{id}',$home,'viewQuestionAction'),'question');
//dashboard routes
$routeManager->addRoute(new Route('get','/dashboard/{action}',$member,'dashboardAction'),'dashboard');
//question routes
$routeManager->addRoute(new Route('post','/question/add',$member,'addQuestionAction'),'postQuestion');
$routeManager->addRoute(new Route('post','/question/edit',$member,'editQuestionAction'),'editQuestion');
$routeManager->addRoute(new Route('get','/question/json/{question_id}',$member,'getJsonQuestionAction'),'jsonQuestion');
$routeManager->addRoute(new Route('get','/question/delete/{question_id}',$member,'deleteQuestionAction'),'deleteQuestion');
//answer routes
$routeManager->addRoute(new Route('post','/answer/add',$member,'postAnswerAction'),'postAnswer');
$routeManager->addRoute(new Route('post','/answer/rate',$member,'rateAnswerAction'),'rateAnswer');
$routeManager->addRoute(new Route('post','/answer/edit',$member,'editAnswerAction'),'editAnswer');
$routeManager->addRoute(new Route('get','/answer/json/{answer_id}',$member,'getJsonAnswerAction'),'jsonAnswer');
$routeManager->addRoute(new Route('get','/answer/delete/{answer_id}',$member,'deleteAnswerAction'),'deleteAnswer');
//subscription routes
$routeManager->addRoute(new Route('get','/subscription/add/{question_id}',$member,'addSubscriptionAction'),'addSubscription');
$routeManager->addRoute(new Route('get','/subscription/delete/{subscription_id}',$member,'deleteSubscriptionAction'),'deleteSubscription');
//user profile routes
$routeManager->addRoute(new Route('get','/profile',$member,'profileAction'),'profile');
$routeManager->addRoute(new Route('post','/profile/edit',$member,'editUserAction'),'editUser');
$routeManager->addRoute(new Route('get','/profile/delete/{user_id}',$member,'deleteUserAction'),'deleteUser');
//login / logout / register routes
$routeManager->addRoute(new Route('post','/login',$member,'loginAction'),'login');
$routeManager->addRoute(new Route('get','/logout',$member,'logoutAction'),'logout');
$routeManager->addRoute(new Route('post','/register',$member,'registerAction'),'register');
$app->register('routes',$routeManager);

$bindings = new Bindings();
$bindings->before('dashboard','is_logged');
$bindings->before('postQuestion','is_logged');
$bindings->before('editQuestion','is_logged');
$bindings->before('jsonQuestion','is_logged');
$bindings->before('deleteQuestion','is_logged');
$bindings->before('postAnswer','is_logged');
$bindings->before('rateAnswer','is_logged');
$bindings->before('editAnswer','is_logged');
$bindings->before('jsonAnswer','is_logged');
$bindings->before('deleteAnswer','is_logged');
$bindings->before('addSubscription','is_logged');
$bindings->before('deleteSubscription','is_logged');
$bindings->before('profile','is_logged');
$app->register('bindings',$bindings);

$app->run();
