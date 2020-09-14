<?php

namespace app\admin\model;

class CategoryBrands  extends Base
{
    public function addCategoryBrands($data) {
        return self::create($data);
    }

    public function getCategoryBrandsList() {
        return self::field('cb.*,c.cate_name,GROUP_CONCAT(b.brand_name) brand_name')->alias('cb')
            ->join('category c','cb.category_id=c.id')
            ->join('brand b','find_in_set(b.id,cb.brands_id)','LEFT')
            ->group('cb.id')
            ->select();
    }

    public function delCategoryBrandsByID($id) {
        return self::destroy($id);
    }

    public function getCategoryBrandsByID($id) {
        return self::find($id);
    }

    public function savrAeditCategoryBrandsByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('pro_img')->find($id);
    }
}