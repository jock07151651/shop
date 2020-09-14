<?php

namespace app\admin\model;

class Category  extends Base
{
    protected $field=true;
    protected static function init(){

        // 新增后
        self::afterInsert(function ($category) {
            $categoryID = $category->id;
            $categoryData = input('post.');
            // 新建分类前，新建分类的推荐位
            if (!empty($categoryData['recpos'])) {
                foreach ($categoryData['recpos'] as $k => $v) {
                    db('rec_item')->insert(['recpos_id' => $v, 'value_id' => $categoryID, 'value_type' => 2]);
                }
            }
        });

        // 更新前
        self::beforeUpdate(function ($category) {
            $categoryID = $category->id;
            $categoryData = input('post.');
            // 根据分类id删除对应分类推荐位
            db('rec_item')->where(['value_id'=>$categoryID,'value_type'=>2])->delete();
            // 新建分类前，新建分类的推荐位
            if (!empty($categoryData['recpos'])) {
                foreach ($categoryData['recpos'] as $k => $v) {
                    db('rec_item')->insert(['recpos_id'=>$v,'value_id'=>$categoryID,'value_type'=>2]);
                }
            }

        });
    }

    public function getCategoryList() {
        return self::order('sort desc')->select();
    }

    public function addAndSavecategory($data,$id='') {
        return self::save($data,$id);
    }

    // 查询对应id的商品分类栏目
    public function getcategoryByID($id) {
        return self::find($id);
    }

    // 查询对应id的商品分类栏目列表
    public function getcategoryListByID($id) {
        return self::where('id',$id)->select();
    }

    // 查询对应id的商品图片
    public function getcategoryImgByID($id) {
        return self::field('cate_img')->find($id);
    }

    public function delcategoryByID($id) {
        return self::destroy($id);
    }

    // 获取顶级栏目列表
    public function getPartitionList() {
        return self::where('pid',0)->select();
    }
}