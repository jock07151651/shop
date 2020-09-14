<?php

namespace app\admin\model;

class MemberLevel  extends Base
{
    public function addMemberLevel($data) {
        return self::create($data);
    }

    public function getMemberLevelList() {
        return self::order('id desc')->paginate(5);
    }

    public function getMemberLevelLists() {
        return self::order('id desc')->select();
    }

    public function delMemberLevelByID($id) {
        return self::destroy($id);
    }

    public function getMemberLevelByID($id) {
        return self::find($id);
    }

    public function addandSavememberLevel($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('MemberLevel_img')->find($id);
    }
}