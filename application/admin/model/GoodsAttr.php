<?php


namespace app\admin\model;


class GoodsAttr extends Base
{
    public function getGattrAndAttrByGID($join,$id) {
        return self::alias('g')->field('g.id,g.attr_id,g.attr_value,a.attr_name')
            ->join($join)->where(['g.goods_id'=>$id,'a.attr_type'=>1])->select();
    }

    // 根据商品id获取商品属性值
    public function getAttrByGoodsID($gID) {
        return self::where('goods_id',$gID)->select();
    }

    // 根据商品属性值id删除属性值
    public function delGoodsAttrByID($good_attrID) {
        return self::destroy($good_attrID);
    }
}