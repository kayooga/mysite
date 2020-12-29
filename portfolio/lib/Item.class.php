<?php

namespace portfolio\lib;

class Item 
{
  public $cateArr = [];
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getCategoryList()
  {
    $table = ' category ';
    $col = ' ctg_id, category_name ';
    $res = $this->db->select($table, $col);
    return $res;
  }

  public function getItemList($ctg_id,$page_num)
  {
    $limit = ' 10 ';
    // $offset = ;
    $table = ' item ';
    $col = ' item_id, item_name, price, image, ctg_id ';
    $where = ($ctg_id !== '') ? ' ctg_id = ?' : '';
    $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];
    $this->db->setLimitOff($limit,$page_num);
    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getItemDetailData($item_id)
  {
    $table = ' item ';
    $col = ' item_id, item_name, detail, price, image, ctg_id';
    $where = ($item_id !== '') ?' item_id = ? ' : '';
    $arrVal = ($item_id !== '') ? [$item_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }
}