<?php
namespace app\index\controller;
use app\index\model\CategoryAd as CategoryAdModel;
use app\index\model\Goods as GoodsModel;
use app\index\model\Category as CategoryModel;
use app\index\model\Article as ArticleModel;
use app\index\model\AlternateImg as AlternateImgModel;


class Index extends Base
{
    public function index() {
        // 前台分类样式
        $show_1200 = 2;
        $show_site = 1;


        // 前台轮播图
        $alternateImg = new AlternateImgModel();
        if (cache('alternateBanner')) {
            $alternateBanner = cache('alternateBanner');
        } else {
            $alternateBanner = $alternateImg->getIndexBanner();
            cache('alternateBanner',$alternateBanner,3600);
        }

        $article = new ArticleModel();
        // 首页公告 / 添加缓存
        if (cache('ament')) {
            // 如果缓存，存在，则将缓存赋给$ament变量
            $ament = cache('ament');
        } else {
            // 配置项的开关缓存，和缓存时间
            $ament = $article->getArticleByCateIDpage(20,3);
            if ($this->config['cache']['value'] == 'yes') {
                cache('ament',$ament,$this->config['cache_time']['value']);
            }
        }


        // 首页促销
        if (cache('promotion')) {
            $promotion = cache('promotion');
        } else {
            $promotion = $article->getArticleByCateIDpage(26,3);
            cache('promotion',$promotion,3600);
        }

        // 热卖推荐，商品
        $goods = new GoodsModel();
        $hosGoodsList = $goods->getRecItemGoodsList(3,1);

        // 首页商品推荐 / 还没逛够
        if (cache('indexGoodsList')) {
            $indexGoodsList = cache('indexGoodsList');
        } else {
            $indexGoodsList = $goods->getRecItemGoodsList(8,1);
            cache('indexGoodsList',$indexGoodsList,3600);
        }


        $category = new CategoryModel();
        // 1. 获取顶级栏目为首页推荐(recID:5)的分类(type:2),
        $recCategory = $category->getTopCategory(5,2);
        // 2. 遍历顶级栏目 / 添加缓存
        if (cache('recCategory')) {
            $recCategory = cache('recCategory');
        } else {
            foreach ($recCategory as $k => $v) {
                // 3. 获取pid为当前id的下级分类
                $recCategory[$k]['child'] = $category->getTopCategory(5,2,$v['id']);
                // 一. 获取二级分类下的子栏目id
                foreach ($recCategory[$k]['child'] as $k1 => $v1) {
                    // 二. 获取二级栏目及其子栏目下的精品推荐商品
                    $recCategory[$k]['child'][$k1]['bastGoods'] = $goods->getIndexRecposGoods($v1['id'],7);
                }

                /*(1) 通过上面获取的一二级栏目，在获取下面所有栏目是新品推荐的商品 */
                $recCategory[$k]['newRecGoods'] = $goods->getIndexRecposGoods($v['id'],4);

                // 获取顶级栏目关联的品牌关联
                $recCategory[$k]['brands'] = $category->getBrandProByCategoryID($v['id']);

                // 获取顶级栏目右侧广告图
                $categoryAd = new CategoryAdModel();
                $recCategory[$k]['position'] = $categoryAd->getCategoryAdImgs($v['id']);
                cache('recCategory',$recCategory,3600);
            }
        }


        $this->assign([
            'show_1200' => $show_1200,
            'show_site' => $show_site,
            'recCategory' => $recCategory,
            'indexGoodsList' => $indexGoodsList,
            'ament' => $ament,
            'promotion' => $promotion,
            'alternateBanner' => $alternateBanner,
        ]);
        return view();
    }



}
