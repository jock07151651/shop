<?php

namespace app\admin\model;

class Nav  extends Base
{

    public function getNavListPage() {
        return self::order('sort desc')->paginate(5);
    }

    public function delNavByID($id) {
        return self::destroy($id);
    }

    public function getNavByID($id) {
        return self::find($id);
    }

    public function savrAeditNavByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('Nav_img')->find($id);
    }
}