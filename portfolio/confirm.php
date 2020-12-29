<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
// use portfolio\lib\Session;
use portfolio\lib\Login;
use portfolio\Common;

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common();
$log = new Login($db);
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
if (isset($_POST['complete']) === true) {
  $mode = 'complete';
}
if (isset($_POST['login']) === true) {
  $mode = 'login';
}
if (isset($_POST['new']) === true) {
  $mode = 'new';
}
if (isset($_POST['next']) === true) {
  $mode = 'next';
}

switch($mode) {
  case 'confirm':
    unset($_POST['confirm']);
    $dataArr = $_POST;

    if(isset($_POST['sex']) === false) {
      $dataArr['sex'] = "";
    }


    $errArr = $common->errorCheck($dataArr);
    $err_check = $common->getErrorFlg();

    $template = ($err_check === true) ? 'confirm.html.twig' : 'member.html.twig';
  break;

  case 'complete':
    unset($_POST['complete']);
    $dataArr = $_POST;
    $table = 'customer';
    $where = 'email = ?';
    $arrwhereVal = [$dataArr['email']];
    $res = $db->update($table,$dataArr, $where, $arrwhereVal);
    if ($res === true) {
        $template = 'login.html.twig';
        foreach ($dataArr as $key => $value){
          $errArr[$key] = '';
        }
    } else {
      $template = 'comfirm.html.twig';
    }
  break;

  case 'back':
    unset($_POST['back']);
    $dataArr = $_POST;

    foreach ($dataArr as $key => $value){
      $errArr[$key] = '';
    }

    $template = 'member.html.twig';
  break;

  case 'login':
    unset($_POST['login']);
    $dataArr = $_POST;
    $email = $_POST['email'];
    $password = $_POST['password'];

    //エラーチェック
    $errArr = $common->loginError($dataArr);
    $err_check = $common->getErrorFlg();

    if ($err_check === true) {
      // $template = 'top.html.twig';
      //ログインの処理
      //一般ユーザー
      $result = $log->checkId($email,$password);//bool
      //管理者
      $master = $log->checkMaster($email,$password);
      // var_dump ($master);
      if ($result === true) {
        header('Location:' .Bootstrap::ENTRY_URL. 'top.php');
        } elseif ($master === true) {
          header('Location:' .Bootstrap::ENTRY_URL. 'top.php');
      } else {
      $errArr['password'] = 'ログインIDまたはパスワードが間違っています';
      $template = 'login.html.twig';
      }

    } else {
      $template = 'login.html.twig';
    }
    
  break;

  case 'new':
    unset($_POST['new']);
    $dataArr = $_POST;
    foreach ($dataArr as $key => $value){
      $errArr[$key] = '';
    }
    $template = 'newAccount.html.twig';
  break;
  
  case 'next':
    unset($_POST['next']);
    $dataArr = $_POST;
    //追加
    $dataArr['regist_date'] = date("Y-m-d H:i:s");
    $errArr = $common->accountError($dataArr);
    $err_check = $common->getErrorFlg();
    if ($err_check === true) {
      unset($dataArr['password_conf']);
      $dataArr['password'] = password_hash($dataArr['password'], PASSWORD_DEFAULT);
      $table = ' customer ';
      $insData = $dataArr;
      $where = 'customer_no = ?';
      $arrWhereVal = [$_SESSION['customer_no']];
      $res = $db->update($table,$insData, $where, $arrWhereVal);
      if ($res === true) {
        $template = 'member.html.twig';
        foreach ($dataArr as $key => $value) {
          $errArr[$key] = '';
        }
      }
    } else {
      $template = 'newAccount.html.twig';
    }
  break;
  default;
  break;

}

$sexArr = initMaster::getSex();
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context['login_user'] = $login_user;
$context['sexArr'] = $sexArr;
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);