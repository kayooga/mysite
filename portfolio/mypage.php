<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;


//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$itm = new Item($db);
$log = new Login($db);
$log->checkSession();

$result = Login::checkLogin();
if (!$result) {
  $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
  header('Location: login.php');
  return;
}
$login_user = $_SESSION['login_user'];
//会員情報の取得
$table = 'customer';
$col = '';
$where = 'email = ?';
$arrVal = [$_SESSION['login_user']['email']];
$res = $db->select($table, $col, $where, $arrVal);
if ($res !== '') {
  $dataArr = $res;
}


$sexArr = initMaster::getSex();
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();
$cateArr = $itm->getCategoryList();

$context = [];
$context['login_user'] = $login_user;
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr[0];

$template = $twig->loadTemplate('mypage.html.twig');
$template->display($context);