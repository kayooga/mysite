<?php
/*ファイルパス：C:\xampp\htdocs\DT\shopping\list.php
ファイル名：list.php(商品一覧を表示するプログラム、controller)
アクセスURL：http://localhost/DT/shopping/list.php
*/

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Item;
use portfolio\lib\Login;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
$itm = new Item($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$log->checkSession();
$result = Login::checkLogin();
if (!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してログインしてください';
}
$login_user = isset($_SESSION['login_user']);

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^\d+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
$cateArr = $itm->getCategoryList();

//ページ数の取得
if(isset($_GET['page_num']) === true && preg_match('/^\d+$/',$_GET['page_num']) === 1) {
    $offset = 10 * $_GET['page_num'] - 1;
    $page_num = $_GET['page_num'];
} else {
    $offset = 0;
    $page_num = 0;
}
//アイテム総数
$table = 'item';
$where = ($ctg_id !== '') ? ' ctg_id = ?' : '';
$arrVal = ($ctg_id !== '') ? [$ctg_id] : [];
$cnt = $db->count($table, $where, $arrVal);
//10アイテムずつ取得
$dataArr = $itm->getItemList($ctg_id,$offset);
// var_dump($cnt);

$context = [];
$context['login_user'] = $login_user;
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$context['page_num'] = $page_num;
$context['cnt'] = $cnt;
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);
