<?php
/*ファイルパス：C:\xampp\htdocs\DT\shopping\lib\PDODatabase.class.php
ファイル名：PDODatabase.class.php(商品に関するプログラムのクラスファイル、Model)
PDO(PHP Data Objects) : PHP標準(5.1.0以降)のDB接続クラス
おすすめの記事：http://qiita.com/7968/items/6f089fec8dde676abb5b
*/

namespace portfolio\lib;

class PDODatabase
{
  private $dbh = null;
  private $db_host = '';
  private $db_user = '';
  private $db_pass = '';
  private $db_name = '';
  private $db_type = '';
  private $order = '';
  private $limit = '';
  private $offset = '';
  private $groupby = '';

  public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    //SQL関連
    $this->order = '';
    $this->limit = '';
    $this->offset = '';
    $this->groupby = '';
  }

  private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    try {
      switch ($db_type) {
        case 'mysql':
          $dsn = 'mysql:host=' .$db_host . ';dbname=' . $db_name;
          $dbh = new \PDO($dsn, $db_user, $db_pass);
          $dbh->query('SET NAMES utf8');
          break;
        
        case 'pgsql':
          $dsn = 'pgsql:dbname=' . $db_name . 'host=' . $db_host . 'port=5432';
          $dbh = new \PDO($dsn, $db_user, $db_pass);
          break;
      }
    } catch (\PDOException $e) {
      var_dump($e->getMessage());
      exit();
    }
    return $dbh;
  }

  public function setQuery($query = '' , $arrVal = [])
  {
    $stmt = $this->dbh->prepare($query);
    $stmt->execute($arrVal);
  }

  public function select($table, $column = '', $where = '', $arrVal = [])
  {
    $sql = $this->getSql('select', $table, $where, $column); 
    // var_dump($sql);
    $this->sqlLogInfo($sql, $arrVal);
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($arrVal); 
    // var_dump($res);
    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    $data = [];
    while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push ($data, $result);
    }
    // var_dump($data);
    return $data;
  }
  public function count($table, $where = '', $arrVal = [])
  {
    $sql = $this->getSql('count', $table, $where);

    $this->sqlLogInfo($sql, $arrVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return intval($result['NUM']);
  }

  public function setOrder($order = '')
  {
    if ($strOrder !== '') {
      $this->order = ' ORDER BY ' . $strOrder;
    }
  }
  public function setLimitOff($limit = '', $offset = '')
  {
    if ($limit !== "") {
      $this->limit = " LIMIT " . $limit;
    }
    if ($offset !== "") {
      $this->offset = " OFFSET " . $offset;
    }
  }
  

  public function setGroupBy($groupby)
  {
    if ($groupby !== '' ) {
      $this->groupby = ' GROUP BY ' . $groupby;
    }
  }

  private function getSql($type, $table, $where = '', $column = '')
  {
    switch ($type) {
      case 'select' :
        $columnKey = ($column !== '') ? $column : "*";
      break;

      case 'count' :
        $columnKey = ' COUNT(*) AS NUM ';
      break;

      default:
    break;
    }

    $whereSQL = ($where !== '') ? ' WHERE ' . $where : '';
    $other = $this->groupby . " " . $this->order . " " .$this->limit . " " .$this->offset;//全部カラ

    //sql文の作成
    $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;
// select COUNT(*) AS NUM from item where ctg_id = 2;
    //$sql= 'SELECT item_id... from item WHERE session_key = ?'; 
    return $sql;
  }
  public function insert($table, $insData = [])
  {
    $insDataKey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = '';
    $prest = '';
//insertData= ['family_name' => '田中' , 'first_name' => '太郎']
    foreach ($insData as $col => $val) {
      $insDataKey[] = $col;//['family_name','first_name']
      $insDataVal[] = $val;//['田中','太郎']
      $preCnt[] = ' ? ';
    }
    $columns = implode(",", $insDataKey);//"family_name, first_name"
    $preSt = implode("," , $preCnt);//"?"
    $sql = " INSERT INTO "
          . $table//session
          ." ( "
          . $columns//"family_name, first_name"
          . ") VALUES ("
          . $preSt //?
          .") ";
    $this->sqlLogInfo($sql, $insDataVal);
//ログを書き込んで、INSERT文準備して、実行する
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($insDataVal);//?="田中,太郎"
    if ($res === false ) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;//trueかfalse
  }

  public function update($table, $insData = [], $where, $arrWhereVal = [])//sql文の用意、ログ残して、実行する
  {
    $arrPreSt = [];
    //$insData = [ 'password' => *** ]
    foreach ($insData as $col => $val) {
      $arrPreSt[] = $col . " =? ";
    }
    $preSt = implode(',' , $arrPreSt);
    //implodeは第一引数と第二引数は逆でもいい
    //$preSt='password = ?'
    //sql文の作成
    $sql = " UPDATE "
          . $table//customer
          ." SET "
          . $preSt//password=?
          ." WHERE "
          . $where;//email = ?
    //配列のVALUEを取り出して1つの配列にする
    $updateData = array_merge(array_values($insData), $arrWhereVal);
    // array_merge = ([***,email])
    // $updateData = [1,3];
    // var_dump($sql);
    // echo '<BR>';
    // var_dump($insData);
    // echo '<BR>';
    // var_dump($arrWhereVal);
    // echo '<BR>';
    // var_dump($updateData);
    // die();
    $this->sqlLogInfo($sql, $updateData);
    //ログふぁるに書き込み

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($updateData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());//エラー文の表示
    }
    return $res;
  }
//DELETE FROM テーブル名 WHERE カラム1 = ?;
//$where = 'customer_no=?'
//$delData = $_SESSION['customer_no']
  public function delete($table, $where, $delData = [])
  {
    $sql = " DELETE "
          ." $table " //customer
          ." WHERE "
          ." $where "; //'customer_no=?'
    
    $this->sqlLogInfo($sql, $delData);
    // $deleteData = array_values($delData);
    // var_dump($deleteData);
    // echo '<br>';
    // $deleteData[] = intval($delData[0]);
    // var_dump($deleteData);
    
    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($delData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }

  public function getLastId()
  {
    return $this->dbh->lastInsertId();
  }

  private function catchError($errArr = [])
  {
    $errMsg = (!empty($errArr[2])) ? $errArr[2] : "";
    die("SQLエラーが発生しました。" . $errArr[2]); //exitと一緒
  }

  private function makeLogFile()
  {
    $logDir = dirname(__DIR__) . "/logs";
    if (!file_exists($logDir)) {//file_exists(このファイルがあるか確認)！なければ
      mkdir($logDir, 777);//make directry
    }
    $logPath = $logDir . '/shopping.log';
    if (!file_exists($logPath)){
      touch($logPath);//ファイルを作る
    }
    return $logPath;//ファイルの文字列
  }

  private function sqlLogInfo($str, $arrVal = [])
  {
    $logPath = $this->makeLogFile();//ファイルの文字列
    $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));//$str=  $arrVal
    error_log($logData, 3, $logPath);//(残すログ,ファイルを残す,書き込むファイル)
  }
}

