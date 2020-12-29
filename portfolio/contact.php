<?php
/*
アクセスURL：http://localhost/DT/portfolio.contact.php
*/

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
// use portfolio\lib\Session;
use portfolio\lib\Login;
use portfolio\Common;
use portfolio\lib\Item;


//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
$common = new Common();
$itm = new Item($db);


$log->checkSession();
$result = Login::checkLogin();
if (!$result) {
  $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
}
$login_user = isset($_SESSION['login_user']);



//初期データを設定
$dataArr = [
  'famiy_name' => '',
  'first_name' => '',
  'email' => '',
  'contact' => ''
];

//エラーメッセージの定義、初期
$errArr = [];
foreach ($dataArr as $key => $value){
  $errArr[$key] = '';
}
// $err_Arr=$dataArr
// if (isset($_POST['confirm']) === true) {
//   unset($_POST['confirm']);
//   $dataArr = $_POST;
//   $errArr = $common->contactError($dataArr);
//   $err_check = $common->getErrorFlg();
  
//   $template = ($err_check === true) ? 'contact_confirm.html.twig' : 'contact.html.twig';
// }
$cateArr = $itm->getCategoryList();




$context = [];
$context['login_user'] = $login_user;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['cateArr'] = $cateArr;

$template = $twig->loadTemplate('contact.html.twig');
$template->display($context);

