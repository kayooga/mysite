<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;
use portfolio\Common;
use portfolio\master\initMaster;



$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$itm = new Item($db);
$common = new Common();

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


if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
}
if (isset($_POST['back']) === true) {
  $mode = 'back';
}
if (isset($_POST['complete']) === true) {
  $mode = 'complete';
}

switch($mode){
  case 'confirm':
    unset($_POST['confirm']);
    $dataArr = $_POST;

    if(isset($_POST['sex']) === false){
      $dataArr['sex'] = "";
    }
    if (isset($_POST['traffic']) === false){
      $dataArr['traffic'] = [];
    }
    $errArr = $common->errorCheck($dataArr);
    $err_check = $common->getErrorFlg();
    $template = ($err_check === true) ?'newStaff_conf.html.twig' : 'staff.html.twig';
  break;

  case 'back':
    $dataArr = $_POST;
    unset($dataArr['back']);
    foreach ($dataArr as $key => $value){
      $errArr[$key] = '';
    }
    $template = 'staff.html.twig'; 
  break;

  case 'complete':
    $dataArr = $_POST;
    unset($dataArr['complete']);
    $dataArr['regist_date'] = date("Y-m-d H:i:s");
    $dataArr['traffic'] = implode('_', $dataArr['traffic']);
    $table = 'staff';
    $res = $db->insert($table, $dataArr);
    if($res === true){
      header('Location:' .Bootstrap::ENTRY_URL. 'staff.php');
      exit();
    } else {
      $template = 'staff.html.twig';

      foreach ($dataArr as $key => $value){
        $errArr[$key] = '';
      }
    }
  break;
}

$sexArr = initMaster::getSex();
$trafficArr = initMaster::getTrafficWay();
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

//staffの情報取得
// $table = 'staff';
// $staffData = $db->select($table);
// unset($staffData[0]);

$cateArr = $itm->getCategoryList();


$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['trafficArr'] = $trafficArr;
$context['dataArr'] = $dataArr;
// $context['staffData'] = $staffData;
$context['errArr'] = $errArr;
$context['cateArr'] = $cateArr;
$context['master'] = $master;
$template = $twig->loadTemplate($template);
$template->display($context);