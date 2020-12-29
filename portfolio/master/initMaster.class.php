<?php

namespace portfolio\master;

class initMaster
{
  public static function getDate()
  {
    $yearArr = [];
    $monthArr = [];
    $dayArr = [];

    $next_year = date('Y') + 1;
    //date:今現在の日付を取得できる

    //年を作成
    for ($i = 1900; $i < $next_year; $i++){
      $year = sprintf("%04d" ,$i);  //$iを４桁で返す
      $yearArr[$year] = $year . '年';
      //$yearArr=['1900' => '1900年']
    }
    //月を作成
    for ($i = 1;$i < 13; $i++){
      $month = sprintf("%02d", $i);  //0埋め 01,02,03....
      $monthArr[$month] = $month . '月';
    }
    //日を作成
    for($i = 1;$i < 32; $i++){
      $day = sprintf("%02d", $i);
      $dayArr[$day] = $day . '日';
    }
    return [$yearArr, $monthArr, $dayArr];
    //regist.phpのgetDate()でこれがreturnされる
  } 
  public static function getSex()
  {
    $sexArr = ['1' => '男性', '2' => '女性'];
    return $sexArr; 
  }
  public static function getTrafficWay()
  {
    $trafficArr = ['徒歩', '自転車', 'バス', '電車', '車・バイク'];
    return $trafficArr;
  }
}