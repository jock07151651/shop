<?php


namespace app\index\controller;
use app\index\model\Cate as CateModel;
use app\index\model\Article as ArticleModel;


class Article extends Base
{
    public function index($artID) {
        // 显示的宽度
        $show_1200 = 1;
        $cate = new CateModel();
        // 获取左侧为普通分类的栏目
        if (cache('comCates')) {
            $comCates = cache('comCates');
        } else {
            $comCates = $cate->getComCate(5);
            cache('comCates',$comCates,3600);
        }

        // 获取左侧网店帮助分类的栏目
        if (cache('helpCates')) {
            $helpCates = cache('helpCates');
        } else {
            $helpCates = $cate->getHelpCate(3);
            cache('helpCates',$helpCates,3600);
        }

        // 通过文章id获取文章
        $article = new ArticleModel();
        // 如果文章使用同一个变量名，再次查询时，只会查询到第一次缓存的文章
        //所以以文章id为动态，为每一篇文章设置缓存
        $artNume = $artID.'_art';
        if (cache($artNume)) {
            $artData = cache($artNume);
        } else {
            $artData = $article->getArticleByID($artID);
            cache($artNume,$artData,3600);
        }


        // 文章面包屑导航
        $artPosition = $article->position($artData['cate_id']);

        $this->assign([
            'show_1200' => $show_1200,
            'comCates' => $comCates,
            'helpCates' => $helpCates,
            'artData' => $artData,
            'artPosition' => $artPosition,
        ]);
        return view('article');
    }
}