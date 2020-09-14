<?php


namespace app\admin\model;


class Attr extends Base
{
    public function delTypeID($tID) {
        return self::where('type_id',$tID)->delete();
    }

    // 添加 或 修改
    public function addAndSaveAttr($data,$id='') {
        return self::save($data,$id);
    }

    // 分页显示属性列表
    public function getAttrListPage($map) {
        return self::alias('a')->field('a.*,t.type_name')
                    ->join('type t','a.type_id=t.id')
                    ->where($map)
                    ->paginate(5);
    }

    // 查询某条属性
    public function getAttrByID($id) {
        return self::find($id);
    }

    // 删除商品属性
    public function delAttrByID($id) {
        return self::destroy($id);
    }

    // 通过类型外键id获取对应数据
    public function getAttrByType_id($typeID) {
        return self::where('type_id',$typeID)->select();
    }

    // 通过商品中的类型id获取对应的属性数据
    public function getGoodsTypeID($typeID) {
        return self::where('type_id',$typeID)->select();
    }



}