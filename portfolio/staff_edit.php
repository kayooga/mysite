<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\master\initMaster;
use portfolio\Common;
use portfolio\lib\Item;


$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common();
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

$staff_id = (isset($_GET['staff_id']) === true && preg_match('/^\d+$/', $_GET['staff_id'])
=== 1) ? $_GET['staff_id'] : '';

$mode = '';

//staff情報の表示
$table = 'staff';
$col = '';
$where = 'staff_id = ?';
$arrVal = [$staff_id];
$res = $db->select($table, $col, $where, $arrVal);
if ($res !== '') {
  $dataArr = $res;
}
$template = 'staff_edit.html.twig';


if (isset($_POST['update']) === true) {
  $mode = 'update';
}
if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}
if (isset($_POST['back']) === true) {
  $mode = 'back';
}
if (isset($_POST['complete']) === true) {
  $mode = 'complete';
}
if (isset($_POST['delete']) === true) {
  $mode = 'delete';
}
if (isset($_POST['del_comp']) === true) {
  $mode = 'del_comp';
}
if (isset($_POST['not_del']) === true) {
  $mode = 'not_del';
}


switch ($mode) {

  case 'update':
    unset($_POST['update']);
    $table = 'staff';
    $col = '';
    $where = 'staff_id = ?';
    $arrVal = [$staff_id];
    $res = $db->select($table, $col, $where, $arrVal);
    if ($res !== '') {
      $dataArr = $res;
    }

    $template = 'staff_edit.html.twig';
  break;
  
  case 'confirm':
    unset($_POST['confirm']);
    $dataArr = [$_POST];
    if(isset($_POST['sex']) === false) {
      $dataArr[0]['sex'] = "";
    }

    $errArr = $common->errorCheck($dataArr[0]);
    $err_check = $common->getErrorFlg();

    $template = ($err_check === true) ? 'staff_conf.html.twig' : 'staff_edit.html.twig';
  break;

  case 'back':
    unset($_POST['back']);
    $dataArr = [$_POST];

    foreach ($dataArr[0] as $key => $value){
      $errArr[$key] = '';
    }

    $template = 'member_edit.html.twig';
  break;

  case 'complete':
  unset($_POST['complete']);
  $dataArr = [$_POST];
  $dataArr[0]['update_date'] = date("Y-m-d H:i:s");
  $table = 'customer';
  $where = 'customer_no = ?';
  $arrwhereVal = [$login_user['customer_no']];
  $res = $db->update($table,$dataArr[0], $where, $arrwhereVal);
  if ($res === true) {
    $_SESSION['msg'] = '変更しました';
    $template = 'mypage.html.twig';
  } else {
    $template = 'member_conf.html.twig';
  }
  break;

  case 'delete':
    unset($_POST['delete']);
    $dataArr = [$_POST];
    $_SESSION['msg'] = '本当に退会しますか？';
    $template = 'del_conf.html.twig';

    // foreach ($dataArr as $key => $value){
    //   $errArr[$key] = '';
    // }
  break;

  case 'del_comp':
    unset($_POST['del_comp']);
    $table = 'customer';
    $where = 'customer_no = ?';
    $delData = [$customer_no];
    $res = $db->delete($table, $where, $delData);
    if ($res === true) {
      // $_SESSION['msg'] = '退会処理が完了しました';
      $template = 'del_conf.html.twig';
      exit('退会処理が完了しました');
    }

  case 'not_del':
    header('Location:' .Bootstrap::ENTRY_URL. 'mypage.php');
  break;

  default;
  break;
}


list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$sexArr = initMaster::getSex();
$cateArr = $itm->getCategoryList();


$context = [];
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['trafficArr'] = initMaster::getTrafficWay();

// $context['msg'] = $_SESSION['msg'];
$context['master'] = $master;
$context['dataArr'] = $dataArr[0];
$context['cateArr'] = $cateArr;
// $context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);