<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;
use portfolio\master\initMaster;


$db = new PDODatabase(Bootstrap::DB_HOST,Bootstrap::DB_USER,Bootstrap::DB_PASS,Bootstrap::DB_NAME,Bootstrap::DB_TYPE);
$itm = new Item($db);
$initMaster = new initMaster();


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

//staffの情報取得
$table = 'staff';
$column = '';
$where = 'staff_id = ?';
$arrVal = [$staff_id];
$dataArr = $db->select($table, $column, $where, $arrVal);
$dataArr[0]['traffic'] = explode('_',$dataArr[0]['traffic']);
// var_dump($dataArr[0]['traffic']);
$cateArr = $itm->getCategoryList();

$context = [];
$context['master'] = $master;
$context['trafficArr'] = initMaster::getTrafficWay();
$context['dataArr'] = $dataArr[0];
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('staff_detail.html.twig');
$template->display($context);