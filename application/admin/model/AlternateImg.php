<?php

namespace app\admin\model;

class AlternateImg  extends Base
{
    public function addAlternateImg($data) {
        return self::create($data);
    }

    public function getAlternateImgList($limit=2) {
        return self::order('sort desc')->paginate($limit);
    }

    public function delAlternateImgByID($id) {
        return self::destroy($id);
    }

    public function getAlternateImgByID($id) {
        return self::find($id);
    }

    public function savrAeditAlternateImgByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('img_src')->find($id);
    }
}