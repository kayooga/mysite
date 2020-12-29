<?php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
// use portfolio\lib\Session;
use portfolio\lib\Login;
use portfolio\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
$itm = new Item($db);

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);
//セッションチェック
$log->checkSession();
Login::checkLogin();
$login_user = isset($_SESSION['login_user']) ? $_SESSION['login_user'] : '';
// var_dump($login_user['customer_no']);

//item_idを取得する
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id'])
=== 1) ? $_GET['item_id'] : '';

//item_idが取得できない場合、商品一覧へ遷移させる
if ($item_id === '') {
  header('Location:' . Bootstrap::ENTRY_URL . 'list.php');
}//ページ遷移

//カテゴリーリスト(一覧)を取得する
$cateArr = $itm->getCategoryList();
//' ctg_id, category_name 
//商品情報を取得する
$itemData = $itm->getItemDetailData($item_id);
//item_id, item_name, detail, price, image, ctg_id';
// var_dump($itemData);
// $itemData[0]['detail'] = nl2br($itemData[0]['detail']);
// str_replace('<br />','<br>',$itemData[0]['detail']);

//コメントの投稿
if (isset ($_POST['send']) === true) {
  $contents = $_POST['contents'];
  if ($login_user === '' ) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
    header('Location: login.php');
    return;
  } elseif ($contents === '') {
    $msg['err_contents'] = 'コメントを記入してください';
  } else {
    $table = 'review';
    $insData = [
      'review_comment' => $contents, 
      'review_date' => date("Y-m-d H:i:s"),
      'item_id' => $item_id,
      'customer_no' => $login_user['customer_no']
    ];
    $res = $db->insert($table, $insData);
    if ($res !== false) {
      $msg['contents'] = '投稿ありがとうございました'; 
    } else {
      $msg['err_contetns'] = '投稿に失敗しました';
    }
  }
} 

//レビューコメントの取得
$table = 'review r LEFT JOIN customer c ON r.customer_no = c.customer_no';
$column = 'r.review_comment,r.review_date,r.item_id,r.customer_no,c.customer_no';
$where = 'item_id = ?';
$arrVal = [$item_id];
$res = $db->select($table, $column, $where, $arrVal);
$msg = [];
if (empty($res)){
  $msg['err_comment'] = '口コミはまだありません';
  $reviewData = '';
} else {
  $reviewData = $res;
  // $context['reviewData'] = $reviewData[0];
}
// var_dump($reviewData);
// var_dump($context['reviewData']);
// var_dump($err);



$context = [];
$context['login_user'] = $login_user;
$context['cateArr'] = $cateArr;
$context['itemData'] = $itemData[0];
$context['reviewData'] = $reviewData;
$context['msg'] = $msg;
$template = $twig->loadTemplate('detail.html.twig');
$template->display($context);