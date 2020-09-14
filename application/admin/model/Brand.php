<?php

namespace app\admin\model;

class Brand  extends Base
{
    public function addBrand($data) {
        return self::create($data);
    }

    public function getBrandList() {
        return self::select();
    }

    public function delBrandByID($id) {
        return self::destroy($id);
    }

    public function getBrandByID($id) {
        return self::find($id);
    }

    public function savrAeditBrandByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('brand_img')->find($id);
    }
}