<?php

namespace app\admin\model;

class Conf  extends Base
{
    public function addandSaveConf($data,$id='') {
        return self::save($data,$id);
    }

    public function confSort($id,$sort) {
        return self::update(['id'=>$id,'sort'=>$sort]);
    }

    public function getConfList() {
        return self::order('sort desc')->paginate(3);
    }

    public function delConfByID($id) {
        return self::destroy($id);
    }

    public function getConfByID($id) {
        return self::find($id);
    }

    public function savrAeditConfByID($data,$id='') {
        return self::save($data,$id);
    }

    public function getImgByID($id) {
        return self::field('Conf_img')->find($id);
    }

    public function getShopConf() {
        return self::where('conf_type',1)->order('sort desc')->select();
    }

    public function getGoodsConf() {
        return self::where('conf_type',2)->order('sort desc')->select();
    }

    // 修改配置项
    public function confSave($name,$val='') {
        return self::where('ename',$name)->update(['value'=>$val]);
    }

    // 修改复选框
    public function getCheck($name) {
        return self::field('ename')->where('form_type',$name)->select();
    }

    // 查询文件
    public function fileSave($name) {
        return self::field('value')->where('ename',$name)->find();
    }
}