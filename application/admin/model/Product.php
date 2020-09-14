<?php


namespace app\admin\model;


class Product extends Base
{
    public function addProductStockAttr($gID,$stock,$attr) {
        return self::insert([
            'goods_id' => $gID,
            'goods_stock' => $stock,
            'goods_attr' => $attr,
        ]);
    }

    public function getProductByGoodsID($gID) {
        return self::where('goods_id',$gID)->select();
    }

    public function delProductByGoodsID($gID) {
        return self::where('goods_id',$gID)->delete();
    }
}