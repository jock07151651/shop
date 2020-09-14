<?php


namespace app\admin\controller;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\MemberLevel as MemberLevelModel;
use app\admin\model\Product;
use app\admin\model\RecItem;
use app\admin\model\Type as TypeModel;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Brand as BrandModel;
use app\admin\model\GoodsAttr as GoodsAttrModel;
use app\admin\model\GoodsPhoto as GoodsPhotoModel;
use app\admin\model\Product as ProductModel;
use app\admin\model\MemberPrice as MemberPriceModel;
use app\admin\model\Attr as AttrModel;
use app\admin\model\Recpos as RecposModel;
use app\admin\model\RecItem as RecItemModel;
use catetree\Catetree;

class Goods extends Base
{
    public function lists() {
        $goods = new goodsModel();
        $join = [
            ['category c','g.category_id = c.id','LEFT'],
            ['brand b', 'g.brand_id = b.id', 'LEFT'],
            ['type t', 'g.type_id = t.id', 'LEFT'],
            ['product p', 'g.id = p.goods_id', 'LEFT'],
        ];
        $goodsList = $goods->getGoodsList($join);
        $this->assign('goodsList',$goodsList);
        return view();
    }

    public function add() {
        if (Request()->isPost()) {
            $goods = new goodsModel();
            $data = input('post.');


//            $validate = validate('goods');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $goods->savrAeditGoodsByID($data);
            if ($result !== false) {
                $this->success('添加商品成功','lists');
            } else {
                $this->error('添加商品失败');
            }
        }
        // 显示会员级别
        $memberLevel = new MemberLevelModel();
        $mList = $memberLevel->getMemberLevelLists();
        // 显示商品类型属性
        $type = new TypeModel();
        $typeList = $type->getTypeLists();

        // 显示所属栏目
        $category = new CategoryModel();
        $cateTree = new Catetree();
        $categoryList = $category->getCategoryList();
        $cateTreeList = $cateTree->catetree($categoryList);
        // 显示所属品牌
        $brand = new BrandModel();
        $brandList = $brand->getBrandList();

        // 显示商品推荐位
        $recpos = new RecposModel();
        $recposList = $recpos->getRecposList(1);

        $this->assign([
            'mList' => $mList,
            'typeList' => $typeList,
            'cateTreeList' => $cateTreeList,
            'brandList' => $brandList,
            'recposList' =>$recposList,
        ]);
        return view();
    }

    public function edit($id) {

        $goods = new goodsModel();
        if (Request()->isPost()) {
            $data = input('post.');

//            $validate = validate('goods');
//            if (!$validate->check($data)) {
//                $this->error($validate->getError());
//            }

            $result = $goods->savrAeditGoodsByID($data,$data['id']);
            if ($result !== false) {
                $this->success('修改商品成功','lists');
            } else {
                $this->error('修改商品失败');
            }
        }
        // 显示会员级别
        $memberLevel = new MemberLevelModel();
        $mList = $memberLevel->getMemberLevelLists();

        // 显示会员价格
        $memberPrice = new MemberPriceModel();
        $_memberPrice = $memberPrice->getMemberPriceByGoodsID($id);
        $memberPriceList = [];
        foreach ($_memberPrice as $k => $v) {
            // 将会员级别id作为下标
            $memberPriceList[$v['mlevel_id']] = $v;
        }

        // 显示商品类型属性
        $type = new TypeModel();
        $typeList = $type->getTypeLists();

        // 显示所属栏目
        $category = new CategoryModel();
        $cateTree = new Catetree();
        $categoryList = $category->getCategoryList();
        $cateTreeList = $cateTree->catetree($categoryList);

        // 显示所属品牌
        $brand = new BrandModel();
        $brandList = $brand->getBrandList();

        // 显示当前修改的商品信息
        $goodsData = $goods->getGoodsByID($id);

        // 显示商品类型下的对应属性
        $attr = new AttrModel();
        $attrList = $attr->getGoodsTypeID($goodsData['type_id']);

        // 显示商品类型下的对应属性值
        $goodsAttr = new GoodsAttrModel();
        $_attrsList = $goodsAttr->getAttrByGoodsID($id);
        $attrsList = [];
        // 将查询到的属性值的attrid设置为下标的三维数组
        foreach ($_attrsList as $k => $v) {
            $attrsList[$v['attr_id']][] = $v;
        }
        // 显示对应商品的相册
        $goodPhoto = new GoodsPhotoModel();
        $photoList = $goodPhoto->getPhotoListByGoodsID($id);

        // 显示推荐位
        $recpos = new RecposModel();
        $recposList = $recpos->getRecposList(1);

        // 通过商品获取对应推荐
        $recItem = new RecItemModel();
        $_myGodsRecList = $recItem->getMyGoodsRecList($id,1);
        $myGodsRecList = [];
        foreach ($_myGodsRecList as $k => $v) {
            // 通过商品获取对应的推荐id
            $myGodsRecList[] = $v['recpos_id'];
        }


        $this->assign([
            'mList' => $mList,
            'typeList' => $typeList,
            'cateTreeList' => $cateTreeList,
            'brandList' => $brandList,
            'goodsData' => $goodsData,
            'memberPriceList' => $memberPriceList,
            'photoList' => $photoList,
            'attrList' => $attrList,
            'attrsList' => $attrsList,
            'recposList' => $recposList,
            'myGodsRecList' => $myGodsRecList,
        ]);
        return view();
    }

    public function del($id) {
        $goods = new goodsModel();
        $result = $goods->delgoodsByID($id);
        if ($result) {
            $this->success('删除商品成功','lists');
        } else {
            $this->error('删除商品失败');
        }
    }

    public function product_stock($goods_id) {
        $product = new ProductModel();
        if (request()->isPost()) {
            $product->delProductByGoodsID($goods_id);
            $data = input('post.');
            $goodsAttr = $data['goods_attr'];
            $goodsStock = $data['goods_stock'];
            foreach ($goodsStock as $k => $v) {
                $Attrs = [];
                foreach ($goodsAttr as $k1 => $v1) {
                    if ($v1[$k] <= 0) {
                        continue 2;
                    }
                    $Attrs[] = $v1[$k]; // v1是颜色属性数组
                }
                sort($Attrs);
                $strAttr = implode(',',$Attrs);
                $product->addProductStockAttr($goods_id,$v,$strAttr);
            }
            $this->success('添加商品库存成功！');

        }
        $goodsAttr = new GoodsAttrModel();
        $join = [
            ['attr a', 'a.id = g.attr_id'],
        ];
        $_radioAttrRes = $goodsAttr->getGattrAndAttrByGID($join,$goods_id);
        foreach ($_radioAttrRes as $k => $v) {
            $radioAttrRes[$v['attr_name']][] = $v;

        }
        $productRes = $product->getProductByGoodsID($goods_id);

        $this->assign([
            'productRes' => $productRes,
            'radioAttrRes' => $radioAttrRes,
        ]);
        return view();
    }

    public function ajaxDelPhoto($id) {
        $goodsPhoto = new GoodsPhotoModel();
        $photoList = $goodsPhoto->getGoodsPhotoByID($id);
        $og_photo = IMG_UPLOADS . DS . $photoList['og_photo'];
        $big_photo = IMG_UPLOADS . DS . $photoList['big_photo'];
        $mid_photo = IMG_UPLOADS . DS . $photoList['mid_photo'];
        $sm_photo = IMG_UPLOADS . DS . $photoList['sm_photo'];
        @unlink($og_photo);
        @unlink($big_photo);
        @unlink($mid_photo);
        @unlink($sm_photo);

        // 删除数据库图片记录
        $result = $goodsPhoto->delGoodsPhotoByID($id);
        if($result) {
            echo 1;
        } else {
            echo 2;
        }
    }


}