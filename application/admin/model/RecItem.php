<?php

namespace app\admin\model;

class RecItem  extends Base
{
    public function addandSaveRecItem($data,$id='') {
        return self::save($data,$id);
    }

    public function getRecItemList() {
        return self::order('id desc')->paginate(5);
    }

    // 获取对应商品，和商品类型推荐位
    public function getMyGoodsRecList($valueID,$valueType) {
        return self::where(['value_id'=>$valueID,'value_type'=>$valueType])->select();
    }

    public function getRecItemLists() {
        return self::select();
    }

    public function delRecItemByID($id) {
        return self::destroy($id);
    }

    public function delCategoryWordsByID($value_type,$id) {
        return self::where(['value_type'=>$value_type,'value_id'=>$id])->delete();
    }

    public function getRecItemByID($id) {
        return self::find($id);
    }

    public function getImgByID($id) {
        return self::field('RecItem_img')->find($id);
    }
}