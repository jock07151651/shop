<?php

namespace app\admin\model;

class CategoryWords  extends Base
{

    public function getCategoryWordsList() {
        return self::alias('cw')->field('cw.*,c.cate_name')
                                     ->join('category c','cw.category_id=c.id')
                                     ->select();
    }

    public function delCategoryWordByID($id) {
        return self::destroy($id);
    }

    public function getCategoryWordsByID($id) {
        return self::find($id);
    }

    public function savrAeditCategoryWordsByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('CategoryWords_img')->find($id);
    }
}