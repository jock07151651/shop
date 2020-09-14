<?php


namespace app\index\model;


class CategoryBrands extends BaseModel
{
    // 通过商品分类栏目id获取推广图
    public function getProAndBrandByCategoryID($categoryID) {
        return self::where('category_id',$categoryID)->find();
    }
}