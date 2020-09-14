<?php


namespace app\index\model;

class Order extends BaseModel
{
    public function addOrder($data,$uid,$recId) {
        $cart = new Cart();
        $goodsPriceCount = $cart->goodsPriceCount($recId);
        $order = self::create([
            'out_trade_no' => time().mt_rand('111111','999999'),
            'user_id' => $uid,
            // 商品下单价格
            'goods_total_price' => $goodsPriceCount,
            // 运费
            'post_spent' => 0,
            // 商品订单实付价格
            'order_total_price' => $goodsPriceCount + 0,
            'payment' => $data['payment'],
            'distribution' => $data['distribution'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'province' => $data['province'],
            'city' => $data['city'],
            'country' => $data['country'],
            'address' => $data['address'],
            'order_time' => time(),
        ]);
        return $order->id;
    }

    public function getOrder($orderID) {
        return self::find($orderID);
    }

    public function updateStatus($order_trade_no) {
        return self::where('order_trade_no',$order_trade_no)->update([
            'pay_status' => 1
        ]);
    }

}