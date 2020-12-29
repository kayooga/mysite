<?php
namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\lib\Item;

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
$itm = new Item($db);

$log->checkSession();
$result = Login::checkLogin();
if (!$result) {
  $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
}
$login_user = isset($_SESSION['login_user']);
$err = '';

if (isset($_POST['send']) === true) {
    unset($_POST['send']);
    $text = str_replace('　', ' ', $_POST['search']);
    $search = explode(' ', $text);
    for ($i = 0; $i < count($search); $i++) {
        $where .= "( item_name LIKE '%$search[$i]%' OR detail LIKE '' OR)";

			if ($i <count($array) -1){
				$where .= " AND ";
			}
    }

    $table = 'item';
    $col = ' item_id, item_name, price, image, ctg_id ';
    // $where = "item_name LIKE ? OR detail LIKE ? OR price LIKE ?";
    $arrVal = ['%' .$_POST['search']. '%', '%' .$_POST['search']. '%', '%' .$_POST['search']. '%'];
    $res = $db->select($table, $col, $where, $arrVal);
    if ($res !== false && count($res) !== 0) {

    } else {
        $err = '該当する商品はありません';
    }
}

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^\d+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

$cateArr = $itm->getCategoryList();

$dataArr = $res;

$context = [];
$context['login_user'] = $login_user;
$context['dataArr'] = $dataArr;
$context['cateArr'] = $cateArr;
$context['err'] = $err;
$template = $twig->loadTemplate('search.html.twig');
$template->display($context);


