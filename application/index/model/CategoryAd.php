<?php


namespace app\index\model;


class CategoryAd extends BaseModel
{
    // 获取栏目广告图
    public function getCategoryAdImgs($categoryID) {
        $_data = self::where('category_id',$categoryID)->select();
        $data= [];
        foreach ($_data as $k => $v) {
            $data[$v['position']][] = $v;
        }
        return $data;
    }
}