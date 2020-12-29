<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;


$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
// $ses = new Session($db);
$log = new Login($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);
$log->checkSession();
$result = Login::checkLogin();
if ($result) {
  header('Location:' .Bootstrap::ENTRY_URL. 'top.php');
  return;
}


$login_err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);

$dataArr = [
  'email' => '',
  'password' => ''
];

$errArr = [];
foreach ($dataArr as $key => $value) {
  $errArr[$key] = '';
}
$cateArr = $itm->getCategoryList();


$context = [];
$context['login_err'] = $login_err;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['cateArr'] = $cateArr;

$template = $twig->loadTemplate('login.html.twig');
$template->display($context);