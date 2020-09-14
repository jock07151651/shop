<?php


namespace app\admin\controller;
use app\admin\model\GoodsAttr as GoodsAttrModel;


class GoodsAttr extends Base
{

    public function AjaxDelGattr() {
        $goods_attrID= input('goods_attrid');
        $attr = new GoodsAttrModel();
        $result = $attr->delGoodsAttrByID($goods_attrID);

        if ($result) {
            echo 1;
        } else {
            echo 2;
        }
    }

}