<?php


namespace app\index\model;


use app\index\model\RecItem as RecItemModel;
use catetree\Catetree;

class GoodsPhoto extends BaseModel
{

    // 查询根据商品id查询商品相册
    public function getGoodsPhotoByID($goodsID) {
        return self::where('goods_id',$goodsID)->select();
    }


}