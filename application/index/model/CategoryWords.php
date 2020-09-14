<?php


namespace app\index\model;


class CategoryWords extends BaseModel
{
    public function getCateWordList($categoryID) {
        return self::where('category_id',$categoryID)->select();
    }

}