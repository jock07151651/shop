<?php


namespace app\index\model;

class MemberPrice extends BaseModel
{
    public function getPriceByLevelIDaGID($levelID,$goodsID) {
        return self::where(['mlevel_id'=>$levelID,'goods_id' => $goodsID])->find();
    }

}