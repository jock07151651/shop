<?php

namespace app\admin\model;

class GoodsPhoto  extends Base
{
    public function delPhotoByGoodID($goodsID) {
        return self::where('goods_id',$goodsID)->delete();
    }

    public function getPhotoListByGoodsID($gID) {
        return self::where('goods_id',$gID)->select();
    }

    public function getGoodsPhotoByID($id) {
        return self::find($id);
    }

    public function delGoodsPhotoByID($id) {
        return self::destroy($id);
    }

}