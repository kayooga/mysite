<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Favorite;
use portfolio\lib\Item;
use portfolio\lib\Login;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$itm = new Item($db);
$fav = new Favorite($db);
$log = new Login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$log->checkLogin();
$result = Login::checkLogin();
if (!$result) {
  $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
  header('Location: login.php');
  return;
}
$login_user = $_SESSION['login_user'];

$customer_no = $login_user['customer_no'];
// $login->checkId();
// $loginId = $_SESSION['email'];

$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/' , $_GET['item_id']) === 1) ? $_GET['item_id'] : '';
$favorite_id = (isset($_GET['favorite_id']) === true && preg_match('/^\d+$/' , $_GET['favorite_id']) === 1) ? $_GET['favorite_id'] : '';

$_SESSION['msg'] = '';

// if ($item_id !== '') {
  //   $res = $fav->insFavoriteData($customer_no, $item_id);
  //   if ($res === false) {
    //     echo "お気に入り登録に失敗しました";
    //     exit();
    //   }
    // } 
    
    
    if ($favorite_id !== '') {
      $res = $fav->delFavoriteData($favorite_id);
    }
    
$data = $fav->getFavoriteData($customer_no);
$itemArr = array_column($data, 'item_id');

if ($item_id !== '') {
  if (in_array($item_id, $itemArr) === false) {
    $res = $fav->insFavoriteData($customer_no, $item_id);
    if ($res === false) {
      $_SESSION['msg'] = 'お気に入り登録に失敗しました';
    }
  } else {
    $_SESSION['msg'] = '登録済みの商品です';
  }
}

// var_dump($item_id);
// var_dump($itemArr);


$dataArr = $fav->getFavoriteData($customer_no);
$cateArr = $itm->getCategoryList();



$context = [];
$context['login_user'] = $login_user;
$context['msg'] = $_SESSION['msg'];
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('favorite.html.twig');
$template->display($context);