<?php


namespace app\index\model;

class AlternateImg extends BaseModel
{
    public function getIndexBanner($limit=4) {
        return self::order('sort desc')->paginate($limit);
    }

}