<?php
/*ファイルパス：C:\xampp\htdocs\DT\portfolio\bootstrap.class.php
ファイル名：Bootstrap.class.php(設定に関するプログラム)
*/

namespace portfolio;

date_default_timezone_set('Asia/Tokyo');

require_once dirname(__FILE__). './../vendor/autoload.php';

class Bootstrap
{
  const DB_HOST = 'localhost';
  const DB_NAME = 'portfolio_db';
  const DB_USER = 'portfolio_user';
  const DB_PASS = 'portfolio_pass';
  
  const DB_TYPE = 'mysql';
  
  const APP_DIR = 'c:/xampp/htdocs/DT/';

  const TEMPLATE_DIR = self::APP_DIR . 'templates/portfolio/';

  const CACHE_DIR = false;

  const APP_URL = 'http://localhost/DT/';

  const ENTRY_URL = self::APP_URL . 'portfolio/';

  public static function loadClass($class)
  {
    $path = str_replace('\\', '/', self::APP_DIR . $class . '.class.php');
    require_once $path;
  }
}

spl_autoload_register([
  'portfolio\Bootstrap',
  'loadClass'
]);