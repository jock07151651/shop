<?php

namespace app\admin\model;

class MemberPrice  extends Base
{
    public function getMemberPriceByGoodsID($gID) {
        return self::where('goods_id',$gID)->select();
    }
}