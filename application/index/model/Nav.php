<?php


namespace app\index\model;

class Nav extends BaseModel
{
    public function getNavList() {
        return self::order('sort desc')->select();
    }

}