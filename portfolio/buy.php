<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Cart;
use portfolio\lib\History;
use portfolio\lib\Item;
use portfolio\lib\Login;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$itm = new Item($db);
$cart = new Cart($db);
$his = new History($db);
$log = new Login($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

//ログインチェック
if (isset($_POST['buy']) === true) {
    $res = $log->checkLogin();
    if ($res === false){
      $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
      header('Location: login.php');
      return;
    } else {
      $login_user = $_SESSION['login_user'];
      $customer_no = $login_user['customer_no'];
    }
}

//item_id,crt_idの取得
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/' , $_GET['item_id']) === 1) ? $_GET['item_id'] : '';
$crt_id = (isset($_GET['crt_id']) === true && preg_match('/^\d+$/' , $_GET['crt_id']) === 1) ? $_GET['crt_id'] : '';

//各商品の個数と単価を取得
$table = 'cart c LEFT JOIN item i ON c.item_id = i.item_id ';
$column = 'c.num, i.price';
$where = 'customer_no = ? AND delete_flg = ?';
$arrVal = [$customer_no,0];
$numPrice= $db->select($table, $column, $where, $arrVal);
foreach($numPrice as $val) {
  $num[] = $val['num'];
  $price[] = $val['price'];
}
$nums = implode(",", $num);
$prices = implode(",", $price);


$msg = '';
if ($item_id !== '') {
  $res = $cart->insCartData($customer_no, $item_id);
  if ($res === false) {
    $msg = "商品購入に失敗しました";
    exit();
  }
}
if ($crt_id !== '') {
  $res = $cart->delCartData($crt_id);
}

if (isset($_POST['sold']) === true) {
  unset($_POST['sold']);
  $dataArr = $_POST;
  $to = $_SESSION['login_user']['email'];
  $title = 'ご注文ありがとうございます。';
  $message = 'ご注文内容 \r\n ';
  $headers = "From: smile.links.nk@gmail.com";

  mb_language("Japanese");
  mb_internal_encoding("UTF-8");

  if(mb_send_mail($to, $title, $message, $headers)) {
    $msg = "ご注文ありがとうございました";
    $cart->afterBuy($customer_no);
    $his->history($customer_no,$item_id,$nums,$prices);
    header('Location:' .Bootstrap::ENTRY_URL. 'cart.php');
  } else {
    $msg = "購入に失敗しました";
  }
}

$dataArr = $cart->getCartData($customer_no);
$cateArr = $itm->getCategoryList();

//$cartSumAndNumData = $cart->getItemAndSumPrice($customer_no);
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);


$context = [];
$context['login_user'] = $login_user;
$context['msg'] = $msg;
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;
$template = $twig->loadTemplate('buy.html.twig');
$template->display($context);