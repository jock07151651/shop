<?php


namespace app\admin\controller;


use think\Controller;

class Base extends Controller
{
    public function clearCache() {
        if (cache(NULL)) {
            $this->success('清空缓存成功',url('admin/index/index'));
        }
    }
}