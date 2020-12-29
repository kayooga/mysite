<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;
use portfolio\lib\History;



$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
// $ses = new Session($db);
$log = new Login($db);
$itm = new Item($db);
$his = new History($db);


$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);


//ログインチェック
$log->checkSession();
$result = Login::checkLogin();
if ($result === true) {
  $login_user = $_SESSION['login_user'];
} else {
  $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
  header('Location: login.php');
  return;
}

//カスタマーnoの取得
$customer_no = $login_user['customer_no'];

//購入履歴の取得
$msg = '';
$res = $his->selectHistory($customer_no);
if (empty($res)) {
  $msg = '購入したものがまだありません';
  $dataArr = '';
  } else {
    $dataArr = $res;
}

$cateArr = $itm->getCategoryList();

$context = [];
$context['login_user'] = $login_user;
$context['msg'] = $msg;
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('history.html.twig');
$template->display($context);