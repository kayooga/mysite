<?php
/*
ファイルパス：C:\xampp\htdocs\DT\member\postcode_search.php
ファイル名：postcode_search.php
*/
namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\Bootstrap;


$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
// $log->checkSession();

if (isset($_GET['zip1']) === true && isset($_GET['zip2']) === true) {
  $table = 'postcode';
  $column = ' pref, city, town ';
  $where = 'zip = ' .$_GET['zip1'] . $_GET['zip2']. ' LIMIT 1 ';

  $res = $db->select($table, $column, $where);
  // var_dump($res);
  //出力結果がajaxに渡される
  echo ($res !== "" && count($res) !== 0) ? $res[0]['pref'] . $res[0]['city'] . $res[0]['town'] : '';
} else {
  echo "no";  //echoの結果がajaxのdataに入る
}