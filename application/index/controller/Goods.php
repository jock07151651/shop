<?php
namespace app\index\controller;
use app\index\model\Goods as GoodsModel;
use app\index\model\GoodsPhoto as GoodsPhotoModel;
use app\index\model\GoodsAttr as GoodsAttrModel;

class Goods extends Base
{
    public function index($goodsID) {

        // 显示的宽度
        $show_1200 = 1;
        // 获取某个商品
        $goods = new GoodsModel();
        $goodsData = $goods->getGoodsByID($goodsID);

        // 通过商品id查询商品相册
        $goodsPhoto = new GoodsPhotoModel();
        $goodsPhotoList = $goodsPhoto->getGoodsPhotoByID($goodsID);

        // 判断商品详情是否右上传到图片
        $goodsThumbArr = [];
        if ($goodsData['og_thumb']) {
            $goodsThumbArr['sm_photo'] = $goodsData['sm_thumb'];
            $goodsThumbArr['mid_photo'] = $goodsData['mid_thumb'];
            $goodsThumbArr['big_photo'] = $goodsData['big_thumb'];
            $goodsThumbArr['og_photo'] = $goodsData['og_thumb'];
        }

        // 将商品详情的图片,跟相册图片合并在一起
        array_unshift($goodsPhotoList,$goodsThumbArr);

        // 根据商品id查询商品属性，和关联属性表
        $gAttr = new GoodsAttrModel();
        $attrList = $gAttr->getGoodsAttr($goodsID);

        $radioAttr = [];
        $uniAttr = [];
        foreach ($attrList as $k => $v) {
            // 1 为单选
            if ($v['attr_type'] == 1) {
                $radioAttr[$v['attr_id']][] = $v;
            } else {
                $uniAttr[] = $v;
            }
        }

        $this->assign([
            'show_1200' => $show_1200,
            'goodsData' => $goodsData,
            'goodsPhotoList' => $goodsPhotoList,
            'radioAttr' => $radioAttr,
            'uniAttr' => $uniAttr,
        ]);
        return view('goods');
    }

    // ajax获取打折后的价格
    public function ajGetMemberPrice($goodID) {
        $goods = new GoodsModel();
        $price = $goods->getMemberPrice($goodID);
        return json($price);
    }
}
