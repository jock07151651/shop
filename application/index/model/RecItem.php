<?php


namespace app\index\model;

class RecItem extends BaseModel
{
    // 获取推荐位类型
    public function getTypeGoodsList($recposID,$valueType) {
        return self::where(['recpos_id'=>$recposID,'value_type'=>$valueType])->select();
    }



}