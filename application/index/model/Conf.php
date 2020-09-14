<?php


namespace app\index\model;

use app\index\controller\Base;


class Conf extends BaseModel
{
    public function getConfList() {
        $_confList = self::field('ename,value')->select();
        $confList = [];
        foreach ($_confList as $k => $v) {
            $confList[$v['ename']] = $v;
        }
        return $confList;
    }
}