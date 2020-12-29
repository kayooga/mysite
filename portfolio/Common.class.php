<?php
/*
ファイルパス：c:\xampp\htdocs\DT\member\lib\Common.php
ファイル名：Common.php
エラーチェック
*/

namespace portfolio;

class Common
{
  private $dataArr = [];
  private $errArr= [];
//初期化
  public function __construct()
  {
  }
  public function errorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    //クラス内のメソッドを読み込む
    $this->createErrorMessage();

    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->sexCheck();
    $this->birthCheck();
    $this->zipCheck();
    $this->addCheck();
    $this->telCheck();
    $this->mailCheck();

    return $this->errArr;
  }
  public function accountError($dataArr)
  {
    $this->dataArr = $dataArr;

    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->mailCheck();
    $this->passCheck();
    $this->passConfCheck();

    return $this->errArr;
  }
  public function loginError($dataArr)
  {
    $this->dataArr = $dataArr;

    $this->mailCheck();
    $this->passCheck();

    return $this->errArr;
  }
  public function contactError($dataArr)
  {
    $this->dataArr = $dataArr;

    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->mailCheck();
    $this->contactCheck();

    return $this->errArr;
  }
  private function createErrorMessage()
  {
    foreach ($this->dataArr as $key =>$val) {
      $this->errArr[$key] = '';
    }
  }
  private function familyNameCheck()
  {
    if($this->dataArr['family_name'] === ''){
      $this->errArr['family_name'] = 'お名前（氏）を入力してください';
    }
  }
  private function firstNameCheck()
  {
    //エラーチェックを入れる
    if($this->dataArr['first_name'] === ''){
      $this->errArr['first_name'] = 'お名前（名）を入力してください';
    }
  }
  private function sexCheck()
  {
    if($this->dataArr['sex'] === ''){
      $this->errArr['sex'] = '性別を選択してください';
    }
  }
  private function birthCheck()
  {
    if($this->dataArr['year'] === ''){
      $this->errArr['year'] = '生年月日の年を選択してください';
    }
    if($this->dataArr['month'] === ''){
      $this->errArr['month'] = '生年月日の月を選択してください';
    }
    if($this->dataArr['day'] === ''){
      $this->errArr['day'] = '生年月日の日を選択してください';
    }
    if(checkdate($this->dataArr['month'], $this->dataArr['day'], $this->dataArr['year']) === false){
      $this->errArr['year'] = '正しい日付を入力してください';
    }
    if(strtotime($this->dataArr['year']. '-' .$this->dataArr['month'].'-' .$this->dataArr['day']) - strtotime('now') > 0){
      $this->errArr['year'] = '正しい日付を入力してください';
    }  //strtotime:引数に年月日をいれるとそれを取得する
  }
  private function zipCheck()
  {
    if(preg_match('/^[0-9]{3}$/', $this->dataArr['zip1']) === 0){
      $this->errArr['zip1'] = '郵便番号の上は半角数字３桁で入力してください';
    }
    if(preg_match('/^[0-9]{4}$/', $this->dataArr['zip2']) === 0){
      $this->errArr['zip2'] = '郵便番号の下は半角数字４桁で入力してください';
    }
  }
  private function addCheck()
  {
    if($this->dataArr['address'] === ''){
      $this->errArr['address'] = '住所を入力してください';
    }
  }
  private function mailCheck()
  {
    if(preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+[a-zA-Z0-9\._-]+$/' , $this->dataArr['email']) === 0){
      $this->errArr['email'] = 'メールアドレスを正しい形式で入力してください';
    }//最初の文字が一桁以上繰り返し、0回以上繰り返し＠最初の英数字が１回以上繰り返し、英数字記号が１回以上繰り返し
  }
  private function telCheck()
  {
    if(preg_match('/^\d{1,6}$/', $this->dataArr['tel1']) === 0 ||
    preg_match('/^\d{1,6}$/', $this->dataArr['tel2']) === 0 ||
    preg_match('/^\d{1,6}$/', $this->dataArr['tel3']) === 0 ||
    strlen($this->dataArr['tel1']. $this->dataArr['tel2']. $this->dataArr['tel3']) >= 12){//strlen文字列の長さを取得
      $this->errArr['tel1'] = '電話番号は、半角数字で11桁以内で入力してください';
    }
  }

  private function passCheck()
  {
    if ($this->dataArr['password'] === '') {
      $this->errArr['password'] = 'パスワードを入力してください';
    }
    if (preg_match('/^\w{8}$/', $this->dataArr['password']) === 0 && $this->dataArr['password'] >= 8) {
      $this->errArr['password'] = '8文字以上の英数字で入力してください';
    }  
  }
  private function passConfCheck()
  {

    if ($this->dataArr['password'] === '') {
      $this->errArr['password_conf'] = '確認用パスワードを入力してください';
    }
    if ($this->dataArr['password'] !== $this->dataArr['password_conf']) {
      $this->errArr['password'] = '確認用パスワードと異なっています';
    }
  }
  private function contactCheck()
  {
    if ($this->dataArr['contact'] === '') {
      $this->errArr['contact'] = 'お問い合わせ内容を入力してください';
    }
  }

  public function getErrorFlg()
  {
    $err_check = true;
    foreach ($this->errArr as $key => $value){
      if ($value !== ''){
        $err_check = false;
      }
    }
    return $err_check;
  }

}