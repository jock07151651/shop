<?php


namespace app\index\model;



class GoodsAttr extends BaseModel
{
    public function getGoodsAttr($goodsID) {
        return self::alias('ga')->field('ga.*,a.attr_name,a.attr_type')
                    ->join('attr a','a.id=ga.attr_id')
                    ->where('ga.goods_id',$goodsID)->select();
    }

    public function getCookieGoodsAttr($attrStrID) {
        return self::alias('ga')->field('ga.*,a.attr_name')
                    ->join('attr a', 'a.id = ga.attr_id')
                    ->where('ga.id','in',$attrStrID)->select();
    }

}