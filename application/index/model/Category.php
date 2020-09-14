<?php


namespace app\index\model;


class Category extends BaseModel
{
    // 1. 显示上级分类 和 对应的下级分类
    public function getCategoryList($pid=0) {
        $category = self::getChildCategoryList($pid);
        foreach ($category as $k => $v) {
            $category[$k]['child'] = self::getChildCategoryList($v['id']);
        }
        return $category;
    }

    // 1.1 显示分类
    public function getChildCategoryList($id) {
        return self::where('pid',$id)->select();
    }

    // 2. 通过商品顶级分类栏目id获取关联品牌和推广图
    public function getBrandProByCategoryID($categoryID) {
        $categoryBrands = new CategoryBrands();
        $brand = new Brand();
        $data = [];
        $proBrand = $categoryBrands->getProAndBrandByCategoryID($categoryID);
        $BrandArr = explode(',',$proBrand['brands_id']);

        foreach ($BrandArr as $k => $v) {
            $data['brand'][] = $brand->getBrandByBrandsID($v);
        }


        $data['pro']['img'] = $proBrand['pro_img'];
        $data['pro']['url'] = $proBrand['pro_url'];
        return $data;
    }

    // 3. 获取首页推荐的顶级栏目  5  2 2
    public function getTopCategory($recposID,$valueType,$pid=0) {
        $recItem = new RecItem();
        // 获取推荐位类型对应的商品 或 分类
        $_recCategory = $recItem->getTypeGoodsList($recposID,$valueType);
        $recCategory = [];
        foreach ($_recCategory as $k => $v) {
            // 获取需要的类型，通过类型里的value_id和pid 获取分类栏目 getCategoryByValueID($v['value_id'],$pid);
            $category = db('category')->where(['id'=>$v['value_id'],'pid'=>$pid])->find();
            if ($category) {
                $recCategory[] = $category;
            }
        }
        return $recCategory;
    }

    // 3.1 获取对应pid的分类栏目
    public function getCategoryByValueID($valueID,$pid=0) {
        return self::where(['id'=>$valueID,'pid'=>$pid])->find();
    }









}