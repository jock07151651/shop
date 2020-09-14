<?php

namespace app\admin\model;

class Recpos  extends Base
{

    public function getRecposListPage() {
        return self::order('sort desc')->paginate(5);
    }

    public function getRecposList($recType) {
        return self::where('rec_type',$recType)->select();
    }

    public function delRecposByID($id) {
        return self::destroy($id);
    }

    public function getRecposByID($id) {
        return self::find($id);
    }

    public function savrAeditRecposByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('Recpos_img')->find($id);
    }
}