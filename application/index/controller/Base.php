<?php


namespace app\index\controller;
use app\index\model\Article as ArticleModel;
use app\index\model\Nav as NavModel;
use app\index\model\Conf as ConfModel;
use app\index\model\Category as CategoryModel;

use think\Controller;

class Base extends Controller
{
    // 公用配置项数组
    public $config;

    public function _initialize(){
        // 获取底部分类文章
        $this->_getFooterArts();
        // 获取顶中中部导航
        $this->_getNav();
        // 站点配置
        $this->_getConfList();

        // 获取分类以及二级分类
        $this->_getCategoryList();
    }

    private function _getFooterArts() {
        $article = new ArticleModel();

        // 设置帮助文章缓存
        if (cache('helpCateRes')) {
            $helpCateRes = cache('helpCateRes');
        } else {
            $helpCateRes = $article->getMFooterArts();
            cache('helpCateRes',$helpCateRes,3600);
        }

        // 店铺文章
        $shopHelpArts = $article->getArticleByCateID(3);

        $this->assign([
            'helpCateRes' =>$helpCateRes,
            'shopHelpArts' => $shopHelpArts,
        ]);
    }

    // 菜单栏目
    private function _getCategoryList() {
        $category = new CategoryModel();
        // 设置右侧菜单缓存
        if (cache('categoryList')) {
            $categoryList = cache('categoryList');
        } else {
            $categoryList = $category->getCategoryList();
            cache('categoryList',$categoryList,3600);
        }

        $this->assign([
            'categoryList' => $categoryList,
        ]);
    }

    private function _getNav() {
        $nav = new NavModel();
        // 设置nav导航缓存
        if (cache('navList')) {
            $navList = cache('navList');
        } else {
            $_navList = $nav->getNavList();
            $navList = [];
            foreach ($_navList as $k =>$v) {
                $navList[$v['pos']][] = $v;
            }
            cache('navList',$navList,3600);
        }


        $this->assign([
            'navList' => $navList,
        ]);
    }

    private function _getConfList() {
        $conf = new ConfModel();
        // 设置站点配置缓存
        if (cache('configs')) {
            $configs = cache('configs');
        } else {
            $configs = $conf->getConfList();
            cache('configs',$configs,3600);
        }

        $this->config = $configs;
        $this->assign([
            'configs' => $configs,
        ]);
    }

}