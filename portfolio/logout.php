<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Login;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);


if (isset($_POST['logout']) === true) {
  Login::logout();
  header('Location:' .Bootstrap::ENTRY_URL. 'top.php');
  
}
$result = Login::checkLogin();
if (!$result) {
  exit('セッションが切れましたので、ログインし直してください');
}


// $context = [];
// $context['login_user'] = $login_user;
// $context['dataArr'] = $dataArr;
// $context['errArr'] = $errArr;

// $template = $twig->loadTemplate('contact.html.twig');
// $template->display($context);