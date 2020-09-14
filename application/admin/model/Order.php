<?php

namespace app\admin\model;

class Order  extends Base
{

    public function getOrderList($limit=10) {
        return self::alias('o')->field('o.*,u.username')->join('user u','u.id=o.user_id')
            ->order('order_time desc')->paginate($limit);
    }

    public function getOrder($id) {
        return self::alias('o')->field('o.*,u.username')->join('user u','u.id=o.user_id')
            ->order('order_time desc')->find($id);
    }

}