<?php

namespace app\admin\model;

class Type  extends Base
{
    public function addandSaveType($data,$id='') {
        return self::save($data,$id);
    }

    public function getTypeList() {
        return self::order('id desc')->paginate(5);
    }

    public function getTypeLists() {
        return self::select();
    }

    public function delTypeByID($id) {
        return self::destroy($id);
    }

    public function getTypeByID($id) {
        return self::find($id);
    }

    public function getImgByID($id) {
        return self::field('Type_img')->find($id);
    }
}