<?php

namespace portfolio\lib;

class Login
{
  public $db = NULL;

    public function __construct($db)
    {
      session_start();
      $this->session_key = session_id();
      $this->db = $db;
    }

  public function checkSession()
  {
    //セッションIDのチェック
    $customer_no = $this->selectSession();

    //セッションIDがある（過去にショッピングカートに来た事がある）
    if ($customer_no !== false) {
      $_SESSION['customer_no'] =  $customer_no; //どのページにいってもこのcustomer_noが取得できる
    } else {
      //セッションIDがない（初めてこのサイトに来ている）
      $res = $this->insertSession();
      if ($res === true) {
        $_SESSION['customer_no'] = $this->db->getLastId();//最後に取得したID
      } else {
        $_SESSION['customer_no'] = '';
      }
    }
  }

  private function selectSession()
  {
    $table = ' customer ';
    $col = 'customer_no';
    $where = ' session_key = ? ';
    $arrVal = [$this->session_key];
//session_key = sesID
// $res = [
//   [
//     'customer_no' => '20';
//   ]
// ]
    $res = $this->db->select($table, $col, $where, $arrVal);
    //PDOの$dataが$resに入る
    return (count($res) !== 0) ? $res[0]['customer_no'] : false;
  }

  private function insertSession()
  {
    $table = ' customer ';
    $insData = ['session_key' => $this->session_key];
    $res = $this->db->insert($table, $insData);

    return $res;
  }

  public function checkId($email,$password)
  {
    $result = false;
    $user = $this->selectId($email,$password);
    //パスワードの照会
    if ($user !== false && password_verify($password, $user['password'])) {
      session_regenerate_id(true);
      $_SESSION['login_user'] = $user;
      // var_dump($_SESSION['login_user']);
      $result = true;
    }
    return $result;
  }

  
  private function selectId($email,$password)
  {
    $table = ' customer ';
    $col = 'customer_no, family_name, email, password ';
    $where = ' email = ? ';
    $arrVal = [$email];
    // var_dump($arrVal);
    // exit();
    $res = $this->db->select($table, $col, $where, $arrVal);
    // var_dump($res);
    return (count($res) !== 0 ) ? $res[0] : false;
  }

  public function checkMaster($email,$password)
  {
    $result = false;
    $master = $this->selectMaster($email);
    //パスワードの照会
    if (password_verify($password, $master["password"])) {
      session_regenerate_id(true);
      $_SESSION['master'] = $master;
      $result = true;
    }
    return $result;
  }

  private function selectMaster($email)
  {
    $table = 'staff';
    $col = 'password';
    $where = 'email = ?';
    $arrVal = [$email];
    $res = $this->db->select($table, $col, $where, $arrVal);
    return (count($res) !== 0) ? $res[0] : false;
  }

  public static function checkLogin()
  {
    $result = false;
    if (isset($_SESSION['login_user']) && $_SESSION['login_user']['family_name'] !== '') {
      return $result = true;
    }
    return $result;
  }

  public static function checkMasterLogin()
  {
    $result = false;
    if (isset($_SESSION['master']) && $_SESSION['master'] !== '' ) {
      return $result = true;
    }
    return $result;
  }

  public static function logout()
  {
    $_SESSION = array();
    session_destroy();
  }

}

