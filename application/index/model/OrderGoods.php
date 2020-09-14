<?php


namespace app\index\model;

class OrderGoods extends BaseModel
{
    public function addOrderGoods($orderID,$data) {
        self::create(
            [
                'order_id' => $orderID,
                'goods_id' => $data['goods_id'],
                'goods_name' => $data['goods_name'],
                'member_price' => $data['member_price'],
                'shop_price' => $data['shop_price'],
                'market_price' => $data['market_price'],
                'goods_attr_id' => $data['goodsid_attrid'],
                'goods_attr_str' => $data['goods_attr_str'],
                'goods_num' => $data['goods_num'],
            ]
        );
    }

}