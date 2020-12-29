<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
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

if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}
if (isset($_POST['back']) === true) {
  $mode = 'back';
}
if (isset($_POST['send']) === true) {
  $mode = 'send';
}
$msg = '';

switch ($mode) {
  case 'confirm':
    unset($_POST['confirm']);
    $dataArr = $_POST;
    $errArr = $common->contactError($dataArr);
    $err_check = $common->getErrorFlg();
    $template = ($err_check === true) ? 'contact_confirm.html.twig' : 'contact.html.twig';
  break;

  case 'back':
    unset($_POST['back']);
    $dataArr = $_POST;
    foreach ($dataArr as $key => $value){
      $errArr[$key] = '';
    }

    $template = 'contact.html.twig';
  break;
  
  case 'send':
    unset($_POST['send']);
    $dataArr = $_POST;
    $to = $_POST['email'];
    $title = 'お問い合わせ内容確認';
    $message = $_POST['contact'].$_POST['family_name'].$_POST['first_name'];
    $headers = "From: smile.links.nk@gmail.com";

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    if(mb_send_mail($to, $title, $message, $headers)) {
      $msg = "メールを送信しました";
    } else {
      $msg = "メールを送信できませんでした";
    }
    $template = 'contact_complete.html.twig';
    foreach ($dataArr as $key => $value){
      $errArr[$key] = '';
    }
  break;
  default;
break;
}


$cateArr = $itm->getCategoryList();

$context = [];
$context['login_user'] = $login_user;
$context['msg'] = $msg;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['cateArr'] = $cateArr;


$template = $twig->loadTemplate($template);
$template->display($context);