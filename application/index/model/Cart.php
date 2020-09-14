<?php


namespace app\index\model;

use app\index\controller\Base;
use app\index\model\Goods as GoodsModel;
use app\index\model\GoodsAttr as GoodsAttrModel;


class Cart extends BaseModel
{
    public function addToCart($goodsID,$goodsAttr='',$goodsNum=1) {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        // key = [1-5,6,7]
        $key = $goodsID. '-' . $goodsAttr;
        // （id和attrid不同，组合的键的值也不同）这个键对应的值 是 商品的数量
        if (isset($cart[$key])) {
            // 如果已经存在某个商品+商品属性的键，则修改他的数量
            $cart[$key] += $goodsNum;
        } else {
            $cart[$key] = $goodsNum;
        }
        $aMonth = time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');
        // array( 1-4,5,6 => xx, 1-4,6 => xx, 2-4,5,6 => xxx )
    }

    // 清空购物车
    public function clearCart() {
        setcookie('cart','',1,'/');
    }

    // 删除购物车一条记录
    public function delCart($goodsAttrID) {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        // key = [1-5,6,7]
        $key = $goodsAttrID;
        // 删除这个键对应的值
        unset($cart[$key]);
        $aMonth = time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');
    }

    // 删除选中的购物车商品记录
    public function delCarts($cartValue) {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        // key = [1-5,6,7]
        $cartArr = explode('@',$cartValue);
        // 循环删除这个$cartArr这个数组的值对应的cookie的键
        foreach ($cartArr as $k => $v) {
            unset($cart[$v]);
        }
        $aMonth = time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');
    }

    // 修改购物车一条记录
    public function updateCart($goodsAttrID,$goodsNum) {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        // key = [1-5,6,7]
        $key = $goodsAttrID;
        // 修改这个键对应的值 是 商品的数量
        $cart[$key] = $goodsNum;
        $aMonth = time()+30*24*3600;
        setcookie('cart',serialize($cart),$aMonth,'/');

    }

    // 获取cookie键对应的商品
    public function getGoodsListInCart($cartValue='') {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        if ($cartValue) {
            $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
            $recIdArr = explode('@',$cartValue);
            foreach ($cart as $k => $v) {
                //将 cookie的键 20-1,2,3 从- 分割，获取商品id 20
                // $arr = explode('-',$k);
                // 如果cookie的商品id不在选中状态
                if (!in_array($k,$recIdArr)) {
                    // 则清除
                    unset($cart[$k]);
                }
            }
        }

        // // array( 1-4,5,6 => xx, 1-4,6 => xx, 2-4,5,6 => xxx )
        $_cart = [];
        foreach ($cart as $k => $v) {
            // $arr[0]就是商品id，如果存在第二个元素，那么$arr[1]就是商品属性的id字符串
            $arr = explode('-',$k);

            // 根据$arr[0]这个商品id，查询对应的商品数据
            $goods = new GoodsModel();
            $goodsData = $goods->getGoodsByID($arr[0]);

            // 通过商品获取会员价格 或 折扣
            $memberPrice = $goods->getMemberPrice($arr[0]);
            $_cart[$k]['goods_name'] = $goodsData['goods_name'];
            $_cart[$k]['mid_thumb'] = $goodsData['mid_thumb'];
            $_cart[$k]['member_price'] = $memberPrice;  // 会员价
            $_cart[$k]['market_price'] = $goodsData['markte_price']; // 市场价
            $_cart[$k]['shop_price'] = $goodsData['shop_price']; // 零售价
            $_cart[$k]['goods_num'] = $v;
            $_cart[$k]['goods_id'] = $arr[0]; // 商品的id
            $_cart[$k]['goods_attr_str']= '' ; // 初始化商品属性
            $_cart[$k]['goodsid_attrid'] = $k;
            // 查询商品id拼接后面的商品属性id
            $gAttr = new GoodsAttrModel();
            if ($arr[1]) {

                // 属性名称     属性值     属性价格
                // 颜色       红色          0       颜色：红色 （￥0元）
                // 尺寸       xxl          1000
                $goodsAttr = $gAttr->getCookieGoodsAttr($arr[1]);
                $goodsAttrStr = [];
                $goodsAttrStrPrice = 0;
                foreach ($goodsAttr as $k1 => $v1) {
                    $goodsAttrStr[] = $v1['attr_name']. ':' . $v1['attr_value'] . '('.$v1['attr_price'].'元)';
                    $goodsAttrStrPrice += $v1['attr_price'];
                }

                $goodsAttrStr = implode('<br />',$goodsAttrStr);
                $_cart[$k]['goods_attr_str'] = $goodsAttrStr;
                $_cart[$k]['member_price'] += $goodsAttrStrPrice;
                // 单价*数量，小计
                $_cart[$k]['sub_total'] = $_cart[$k]['member_price'] * $v;
            }
        }
        return $_cart;
    }

    // 购物车数据改动时，计算选中的商品的总价格，节省价格，总数量
    // $rec_id 为选中的商品的id字符串：1，2，3
    public function AjaxCartGoodsAmount($rec_id) {
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        $goods = new GoodsModel();
        $_cart = [];
        $_cart['subtotal_number'] = 0;      //商品总数
        $_cart['save_total_amount'] = 0;    //优惠节省总金额
        $_cart['goods_amount'] = 0;         // 商品节省后总金额
        $_cart['shop_amount'] = 0;         // 商品零售价

        // 将id字符串分割为数组[0=>1,1=>2,2=>3]
        $recIdArr = explode('@',$rec_id);
        foreach ($cart as $k => $v) {
            //将 cookie的键 20-1,2,3 从- 分割，获取商品id 20
            // $arr = explode('-',$k);
            // 如果cookie的商品id不在选中状态
            if (!in_array($k,$recIdArr)) {
                // 则清除
                unset($cart[$k]);
            }
        }

        // 开始计算选中的cookie商品信息
        foreach($cart as $k => $v) {
            // 计算商品总数
            $_cart['subtotal_number'] += $v;
            // 计算商品总会员价（含属性价格） 将 cookie的键 20-1,2,3 从- 分割，获取商品id 20
            $arr = explode('-',$k);
            // 通过商品id，查询某个商品会员价格 或折扣
            $memberPrice = $goods->getMemberPrice($arr[0]);

            //查询每一个商品的零售价
            $shopPrice = $goods->getGoodsPriceByID($arr[0]);

            // 判断该商品是否右商品属性
            if ($arr[1]) {
                $goodsAttrPrice = 0;
                // 通过商品属性id 查询商品属性表id对应的商品属性
                $goodsAttrRes = db('goods_attr')->field('attr_price')->where('id','in',$arr[1])->select();
                foreach ($goodsAttrRes as $k1 => $v1) {
                    // 每一个属性的价格，加到$goodsAttrPrice变量中
                    $goodsAttrPrice += $v1['attr_price'];
                }
                // 再将属性价格与会员价加起来
                $memberPrice += $goodsAttrPrice;
                // 与零售价加起来
                $shopPrice += $goodsAttrPrice;
            }
            // 每得到商品的会员价，递加起来
            $_cart['goods_amount'] += $memberPrice * $v;
            // 每得到商品的零售价，递加起来
            $_cart['shop_amount'] += $shopPrice * $v;

        }
        // 计算商品总节省
        $_cart['save_total_amount'] = $_cart['shop_amount'] - $_cart['goods_amount'];
        return $_cart;
    }

    // 选择下单的商品属性字符串id，获取下单商品实付总价
    public function goodsPriceCount($cartValue) {
        $goodPriceCount = 0;
        // 判断是否已经设置了cookie，是，便把数据反序列为数组，否则给他一个新数组
        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
        if ($cartValue) {
            $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
            $recIdArr = explode('@',$cartValue);
            foreach ($cart as $k => $v) {
                //将 cookie的键 20-1,2,3 从- 分割，获取商品id 20
                // $arr = explode('-',$k);
                // 如果cookie的商品id不在选中状态
                if (!in_array($k,$recIdArr)) {
                    // 则清除
                    unset($cart[$k]);
                }
            }
            $_cart = [];
            foreach ($cart as $k => $v) {
                // $arr[0]就是商品id，如果存在第二个元素，那么$arr[1]就是商品属性的id字符串
                $arr = explode('-',$k);

                // 根据$arr[0]这个商品id，查询对应的商品数据
                $goods = new GoodsModel();

                // 通过商品获取会员价格 或 折扣
                $memberPrice = $goods->getMemberPrice($arr[0]);
                $_cart[$k]['member_price'] = $memberPrice;  // 会员价
                // 查询商品id拼接后面的商品属性id
                $gAttr = new GoodsAttrModel();
                if ($arr[1]) {

                    // 属性名称     属性值     属性价格
                    // 颜色       红色          0       颜色：红色 （￥0元）
                    // 尺寸       xxl          1000
                    $goodsAttr = $gAttr->getCookieGoodsAttr($arr[1]);
                    $goodsAttrStr = [];
                    $goodsAttrStrPrice = 0;
                    foreach ($goodsAttr as $k1 => $v1) {
                        $goodsAttrStr[] = $v1['attr_name']. ':' . $v1['attr_value'] . '('.$v1['attr_price'].'元)';
                        $goodsAttrStrPrice += $v1['attr_price'];
                    }
                    $_cart[$k]['member_price'] += $goodsAttrStrPrice;
                }
                $goodPriceCount += $_cart[$k]['member_price']*$v;
            }
            return $goodPriceCount;
        } else {
            return $goodPriceCount;
        }
    }
}