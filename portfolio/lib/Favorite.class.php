<?php

namespace portfolio\lib;

class Favorite
{
    private $db = null;

    public function __construct($db = null)
    {
      $this->db = $db;
    }

    public function insFavoriteData($customer_no, $item_id)
    {
      $table = ' favorite ';
      $insData = [
        'customer_no' => $customer_no,
        'item_id' => $item_id
      ];
      return $this->db->insert($table, $insData);
      
    }
    //カートの情報を取得する(必要な情報は、誰が$customer_no。必要な商品情報は名前、商品画像、金額)
    public function getFavoriteData($customer_no)
    {
      // SELECT
      // c.crt_id,
      // i.item_id,
      // i.item_name,
      // i.price,
      // i.image';
      // FROM
      // cart c
      // LEFT JOIN
      // item i
      // ON 
      // c.item_id = i.item_id';
      // WHERE
      // c.customer_no = ? AND c.delete_flg = ? ';
      $table = ' favorite f LEFT JOIN item i ON f.item_id = i.item_id ';
      // LEFT JOIN cartを基準にくっつける
      $column = ' f.favorite_id, i.item_id, i.item_name, i.price, i.image ';
      $where = ' f.customer_no = ? AND f.delete_flg = ? ';
      $arrVal = [$customer_no, 0];//?の部分

      return $this->db->select($table, $column, $where, $arrVal);
    }

    //カート情報を削除する : 必要な情報はどのカートを($crt_id)
    public function delFavoriteData($favorite_id)
    {
      $table = ' favorite ';
      $insData = ['delete_flg' => 1];
      $where = ' favorite_id = ? ';
      $arrWhereVal = [$favorite_id];

      return $this->db->update($table, $insData, $where, $arrWhereVal);
    }


    //こっからはいらないかも
    //アイテム数と合計金額を取得する
    public function getItemAndSumPrice($customer_no)
    {
      // 合計金額
      // SELECT
      // SUM( i.price ) AS totalPrice ";
      // FROM
      // cart c
      // LEFT JOIN
      // item i
      // ON
      // c.item_id = i.item_id "
      // WHERE
      // c.customer_no = ? AND c.delete_flg =?';
      $table = " cart c LEFT JOIN item i ON c.item_id = i.item_id ";
      $column = " SUM( i.price ) AS totalPrice ";
      $where = ' c.customer_no = ? AND c.delete_flg = ? ';
      $arrWhereVal = [$customer_no, 0];

      $res = $this->db->select($table, $column, $where, $arrWhereVal);
      $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;
      
      //アイテム数
      $table = ' cart c ';
      $column = ' SUM(num) AS num ';
      $res = $this->db->select($table, $column, $where, $arrWhereVal);

      $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
      return [$num, $price];
    }
    public function afterBuy($customer_no)
    {
      $table = ' cart ';
      $insData = ['delete_flg' => 1];
      $where = ' customer_no = ? ';
      $arrWhereVal = [$customer_no];

      return $this->db->update($table, $insData, $where, $arrWhereVal);
    }
}