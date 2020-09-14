<?php


namespace app\member\model;


class MemberLevel extends BaseModel
{
    public function getUserLevel($points) {
        return self::where('bom_point', '<=',$points)->where('top_point','>=',$points)->find();
    }

}
//0 <= 999 and 9999999 >= 999999999