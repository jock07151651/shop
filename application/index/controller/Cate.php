<?php
namespace app\index\controller;
use app\index\model\Article as ArticleModel;
use app\index\model\Cate as CateModel;
use catetree\Catetree;

class Cate extends Base
{
    public function index($cateID) {
        // 显示的宽度
        $show_1200 = 1;

        // 实例对象
        $cate = new CateModel();
        $cateTree = new Catetree();
        $article = new ArticleModel();

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


        // 查询当前cateID栏目
        $cateData = $cate->getCateByID($cateID);

        // 获取栏目分类的id和子分类的id
        $IDs = $cateTree->childrenIDs($cateID,$cate);
        $IDs[] = intval($cateID);

        // 获取栏目分类下的文章
        $map['cate_id'] = array('IN',$IDs);
        $artListNum = $cateID.'_art';
        // 点击栏目获取文章列表
        if (cache($artListNum)) {
            $artList = cache($artListNum);
        } else {
            $artList = $article->getArticleByCateIDs($map);
            cache($artListNum,$artList,3600);
        }

        // 获取面包屑导航
        $position = $cate->position($cateID);

        $this->assign([
            'show_1200' => $show_1200,
            'comCates' => $comCates,
            'helpCates' => $helpCates,
            'cateData' => $cateData,
            'artList' => $artList,
            'position' => $position,
        ]);
        return view('cate');
    }
}
