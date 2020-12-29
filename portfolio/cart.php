<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Cart;
use portfolio\lib\Item;
use portfolio\lib\Login;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$itm = new Item($db);
$cart = new Cart($db);
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
$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/' , $_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';
// $num = (isset($_POST['num']) === true &&
// preg_match('/^\d+$/', $_POST['num']) === 1) ? $_POST['num'] : '';
// var_dump ($_GET);
// die();

if ($item_id !== '') {
  $res = $cart->insCartData($customer_no, $item_id);
  if ($res === false) {
    echo "商品購入に失敗しました";
    exit();
  }
}

// if ($num !== '') {
//   $table = 'cart';
//   $insData = [$num];
// }

if ($crt_id !== '') {
  $res = $cart->delCartData($crt_id);
}


$dataArr = $cart->getCartData($customer_no);
$cateArr = $itm->getCategoryList();

//$cartSumAndNumData = $cart->getItemAndSumPrice($customer_no);
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);

$context = [];
$context['login_user'] = $login_user;
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('cart.html.twig');
$template->display($context);