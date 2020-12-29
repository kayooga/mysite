<?php

namespace portfolio\lib;

class History
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  public function insHistory($customer_no,$item_id,$nums,$prices)
  {
    //購入履歴用のDBにインサートする
    $table = 'history';
    $insData = [
        'customer_no' => $customer_no,
        'item_id' => $item_id,
        'num' => $num,
        'price' => $prices,
        'order_date' => date("Y-m-d H:i:s")
    ];
  }

  public function selectHistory($customer_no)
  {
    $table = 'history h LEFT JOIN item i ON h.item_id = i.item_id';
    $culumn = 'h.order_id, h.item_id, h.num, h.order_date, h.price, i.item_name, i.image';
    $where = 'h.customer_no = ?';
    $arrVal= [$customer_no];
    return $this->db->select($table,$culumn,$where,$arrVal);
  }
}
