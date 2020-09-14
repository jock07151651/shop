<?php

namespace app\admin\model;

class Link  extends Base
{
    // 添加 或 修改图片
    public function addAndSaveLink($data,$id='') {
        return self::save($data,$id);
    }

    // 显示link列表
    public function getLinkList() {
        return self::order('sort desc')->paginate(3);
    }

    // 通过id获取logo
    public function  getLinkByID($id) {
        return self::field('logo')->find($id);
    }

    // 拖过id获取链接
    public function getLinkdByID($id) {
        return self::find($id);
    }

    // 通过id删除链接
    public function delLinkByID($id) {
        return self::destroy($id);
    }
}