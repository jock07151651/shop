<?php


namespace app\index\model;

class Article extends BaseModel
{
    // 获取帮助分类下的文章
    public function getMFooterArts() {
        $cate = new Cate();
        // 获取cate栏目表，cate_type为3的栏目
        $helpCateRes = $cate->getCateByCateType(3);
        foreach ($helpCateRes as $k => $v) {
            // 当前栏目id 与 文章表的cate_id 对应，便存入
            $helpCateRes[$k]['arts'] = self::getArticleByCateID($v['id']);
        }
        return $helpCateRes;
    }

    // 通过文章id获取文章详情
    public function getArticleByID($artID) {
        return self::find($artID);
    }

    // 通过文章所属栏目id获取文章
    public function getArticleByCateID($cateID) {
        return self::where('cate_id',$cateID)->select();
    }

    public function getArticleByCateIDpage($cateID,$limit) {
        return self::where('cate_id',$cateID)->limit($limit)->select();
    }

    // 通过文章所属栏目id组获取文章
    public function getArticleByCateIDs($map) {
        return self::where($map)->select();
    }

    // 面包屑导航
    public function position($cateID){
        $cate = new Cate();
        $cateList = $cate->getCateList();
        return self::_position($cateList,$cateID);
    }

    private function _position($cateList,$cateID) {
        $cate = new Cate();
        static $arr = [];
        $cateData = $cate->getCateByID($cateID);
        if (empty($arr)) {
            $arr[] = $cateData;
        }
        foreach ($cateList as $k => $v) {
            if ($cateData['pid'] == $v['id']) {
                $arr[] = $v;
                self::_position($cateList,$v['id']);
            }
        }

        return array_reverse($arr);
    }

}