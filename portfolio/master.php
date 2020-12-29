<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;


$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$itm = new Item($db);


$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);

session_start();
$result = Login::checkMasterLogin();
if ($result === true) {
  $master = $_SESSION['master'];
} else {
  $master = '';
}


$cateArr = $itm->getCategoryList();

$context = [];
$context['master'] = $master;
$context['cateArr'] = $cateArr;

$template = $twig->loadTemplate('master.html.twig');
$template->display($context);