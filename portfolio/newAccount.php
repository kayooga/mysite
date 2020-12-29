<?php
// アクセスURL：http://localhost/DT/portfolio/newAccount.php

namespace portfolio;

require_once dirname(__FILE__). '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Login;
use portfolio\master\initMaster;
use portfolio\Common;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$log = new Login($db);
$common = new Common();

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader , [
  'cache' => Bootstrap::CACHE_DIR
]);

// $log->checkSession();

// if (isset($_POST['next']) === true) {
//     unset($_POST['next']);
//     $dataArr = $_POST;
//     $errArr = $common->accountError($dataArr);
//     $err_check = $common->getErrorFlg();
//     //DBsessionに登録
//     if ($err_check === true) {
//         $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
//         $table = ' session ';
//         $res = $db->insert($table,$dataArr);
//         // $insData = ['password' => $_POST['password']];
//         // $where = ' email = ? ';
//         // $arrWhereVal = [$_POST['email']];
//         // $res = $db->update($table, $insData, $where, $arrWhereVal);
//         if ($res === true) {
//           // header('Location:' .Bootstrap::ENTRY_URL. 'member.php');
//           $template = 'member.html.twig';
//           exit();
//         } else {
//           $template = 'newAccount.html.twig';
    
//           foreach ($dataArr as $key => $value) {
//             $errArr[$key] = '';
//           }
//         }
//     } else {
//       $template = 'newAccount.html.twig';
//     }
// }


$context = [];
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);