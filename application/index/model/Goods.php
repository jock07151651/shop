<?php


namespace app\index\model;


use app\index\model\RecItem as RecItemModel;
use catetree\Catetree;

class Goods extends BaseModel
{
    // 查询商品
    public function getRecGoods($map,$limit='') {
        return self::where($map)->limit($limit)->select();
    }

    // 查询某个商品
    public function getGoodsByID($goodsID) {
        return self::find($goodsID);
    }

    // 查询某个商品零售价
    public function getGoodsPriceByID($goodsID) {
        $goods = self::field('shop_price')->find($goodsID);
        return $goods['shop_price'];
    }


    // 热卖推荐，商品
    public function getRecItemGoodsList($recposID,$valueType) {
        $recItem = new RecItem();
        // 查询推荐位中间表，获取商品推荐，热卖的商品
        $_hotIds = $recItem->getTypeGoodsList($recposID,$valueType);

        // 循环将存在推荐位中间的商品id，存为一个数组中
        $hotIds = [];
        foreach($_hotIds as $k => $v) {
            // 获取推荐类型位商品的id
            $hotIds[] = $v['value_id'];
        }

        // 使用推荐中间获取的商品id往商品表获取对应商品
        $map['id'] = ['IN',$hotIds];
        $hotGoodsRes = self::getRecGoods($map);
        return $hotGoodsRes;
    }

    // 获取顶级栏目为首页推荐(recID:5)的分类(type:2),
    public function getIndexRecposGoods($cateID,$recposID) {
        // (1). 传入当前栏目的id，获取当前栏目的下级栏目id
        $cateTree = new Catetree();
        $category = new Category();
        $sonIDs = $cateTree->childrenIDs($cateID,$category);
        $sonIDs[] = $cateID;

        // (2). 获取rec_item表 新品推荐位里的商品信息
        $recItem = new RecItemModel();
        // (3.1) 根据条件，筛选出对应商品数据
        $_RecGoods = $recItem->getTypeGoodsList($recposID,1);
        $RecGoods = [];
        foreach ($_RecGoods as $kk => $vv) {
            // (3.2) 将查询出来的商品数据中的id单独提出来作为一个数组
            $RecGoods[] = $vv['value_id'];
        }
        $map['category_id'] = array('IN',$sonIDs);
        $map['id'] = array('IN',$RecGoods);
        // 查询，对应商品id 和 分类id 的商品
        $goodsRes =  self::getRecGoods($map,6);

        return $goodsRes;
    }

    // 获取会员价格 或 折扣
    public function getMemberPrice($goodsID) {
        $levelID = session('user_level_id'); // 会员id
        $levelRate = session('user_rate');  // 会员对应得折扣
        $goodsData = self::getGoodsByID($goodsID);

        $memberPrice = new MemberPrice();
        if ($levelID) {
            // 获取对应等级id 和 商品id 的价格
            $mPrice = $memberPrice->getPriceByLevelIDaGID($levelID,$goodsID);
            // 获取的该条记录是否设置了会员价格
            if ($mPrice['mpprice']) {
                // 取会员价格
                $price = $mPrice['mpprice'];
            } else {
                // 取折扣价格
                $levelRate = $levelRate / 100;
                $price = $goodsData['shop_price'] * $levelRate;
            }

        } else {
            $price = $goodsData['shop_price'];
        }
        return $price;
    }


}