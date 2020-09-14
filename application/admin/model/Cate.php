<?php

namespace app\admin\model;

class Cate  extends Base
{
    public function getCateByID($id) {
        return self::find($id);
    }

    public function addAndeditCate($data,$id='') {
        return self::save($data,$id);
    }

    // 显示cate列表
    public function getCateList() {
        return self::order('sort DESC')->select();
    }

    public function getCatePID($pid) {
        return self::field('pid')->find($pid);
    }

    public function delBateByID($IDs) {
        return self::destroy($IDs);
    }


}