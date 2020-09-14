<?php
namespace app\index\controller;
use app\index\model\Category as CategoryModel;
use app\index\model\CategoryWords as CategoryWordsModel;
use think\Config;

class Category extends Base
{
    public function index() {

        return view('category');
    }

    // 菜单Ajax获取显示
    public function getCategoryInfo($id) {
        $category = new CategoryModel();
        // 获取当前栏目与下一级栏目
        $categoryList = $category->getCategoryList($id);
        // 获取到推广图和品牌
        $proBrandList = $category->getBrandProByCategoryID($id);

        // 1. 遍历获取的分类
        $cat = '';
        foreach ($categoryList as $k => $v) {
            $cat .= '<dl class="dl_fore1"><dt><a href="'.url('Category/index',['id'=>$v['id']]).'" target="_blank">'.$v['cate_name'].'</a></dt><dd>';
            // 遍历二级分类下的三级分类
            foreach ($v['child'] as $k1 => $v1) {
                $cat .=  '<a href="'.url('Category/index',['id'=>$v['id']]).'" target="_blank">'.$v1['cate_name'].'</a>';
            }
            // 拼接
            $cat .= '</dd></dl><div class="item-brands"><ul></ul></div><div class="item-promotions"></div>';
        }

        // 2. 遍历获取推荐词
        $categoryWord = new CategoryWordsModel();
        $cateWordList = $categoryWord->getCateWordList($id);
        $topic = '';
        foreach ($cateWordList as $k => $v) {
            $topic .= '<a href="'.$v['link_url'].'" target="_blank">'.$v['word'].'</a>';
        }

        // 3. 遍历获取推荐图 和 品牌
        $brands = '';
        foreach ($proBrandList['brand'] as $k => $v) {
            $brands .= '<div class="cate-brand">
                        <div class="img">
	            		    <a href="'.$v['brand_url'].'" target="_blank" title="'.$v['brand_name'].'"><img src="'.config('view_replace_str.__UPLOADS__').DS.$v['brand_img'].'"></a>
	            	    </div>
	            	</div>';
        }


        $brands.='<div class="cate-promotion">
	        <a href="'.$proBrandList['pro']['url'].'" target="_blank"><img width="199" height="97" src="'.config('view_replace_str.__UPLOADS__').DS.$proBrandList['pro']['img'].'"></a>
	    </div>';


        $data = [];
        $data['topic_content'] = $topic;
        $data['cat_content'] = $cat;
        $data['brands_ad_content'] = $brands;
        $data['cat_id'] = $id;
        return json($data);
    }
}
