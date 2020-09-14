<?php

namespace app\admin\model;

class CategoryAd  extends Base
{
    public function addCategoryAd($data) {
        return self::create($data);
    }

    public function getCategoryAdList() {
        return self::field('ca.*,c.cate_name')->alias('ca')
            ->join('category c','ca.category_id=c.id')
            ->group('ca.id')
            ->paginate(5);
    }

    public function delCategoryAdByID($id) {
        return self::destroy($id);
    }

    public function getCategoryAdByID($id) {
        return self::find($id);
    }

    public function savrAeditCategoryAdByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('img_src')->find($id);
    }

    // 查询B，C栏目广告位
    public function getCategoryBmC($position) {
        return self::where('position',$position)->select();
    }
}