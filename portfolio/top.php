<?php

// アクセスURL：http://localhost/DT/portfolio/top.php


namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;



$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$log = new Login($db);
$itm = new Item($db);


$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);

$log->checkSession();

$result = Login::checkLogin();
if ($result === true) {
  $login_user = $_SESSION['login_user'];
} else {
  $login_user = '';
}
$res = Login::checkMasterLogin();
if ($res === true) {
  $master = $_SESSION['master'];
} else {
  $master = '';
}
// var_dump($_SESSION['master']);

$ctg_id = '';
$offset = '';
$dataArr = $itm->getItemList($ctg_id,$offset);



shuffle ($dataArr);
foreach($dataArr as $item){
  $dataArr[] = $item;
}


$cateArr = $itm->getCategoryList();

$context = [];
$context['login_user'] = $login_user;
$context['master'] = $master;
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;

$template = $twig->loadTemplate('top.html.twig');
$template->display($context);
