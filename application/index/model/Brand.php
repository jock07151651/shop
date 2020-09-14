<?php


namespace app\index\model;

use app\index\controller\Base;


class Brand extends BaseModel
{
    // 通过品牌关联中的品牌id获取数据
    public function getBrandByBrandsID($brandsID) {
        return self::find($brandsID);
    }

    // 查询品牌
    public function getBrandListPage($limit=17) {
        return self::order('id desc')->paginate($limit);
    }
}